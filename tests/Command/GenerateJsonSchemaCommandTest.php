<?php

namespace Soyuka\JsonSchemaBundle\Tests\Constraints;

use Soyuka\JsonSchemaBundle\Tests\KernelTrait;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Soyuka\JsonSchemaBundle\Command\GenerateJsonSchemaCommand;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class GenerateJsonSchemaCommandTest extends \PHPUnit_Framework_TestCase
{
    use KernelTrait;
    private $command;
    private $fixture = __DIR__.'/../Fixtures/Resources/validators/TestBundle';

    public function setUp()
    {
        $this->boot();
        $this->rmFixture();
    }

    /**
     * @after
     */
    protected function rmFixture()
    {
        if (!file_exists($this->fixture)) {
            return;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->fixture, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

        rmdir($this->fixture);
    }

    public function getCommand(): CommandTester
    {
        if ($this->command) {
            return $this->command;
        }

        $application = new Application($this->kernel);
        $application->add(new GenerateJsonSchemaCommand());
        $this->command = new CommandTester($application->find('jsonschema:generate'));

        return $this->command;
    }

    public function testExecuteNoArgs()
    {
        $command = $this->getCommand();

        $command->execute([]);

        $d = explode(PHP_EOL, $command->getDisplay());

        $this->assertEquals('Processing bundle TestBundle', $d[0]);
        $this->assertRegexp('/^Directory .+ has been created/', $d[1]);
        $this->assertStringStartsWith('Schema for entity Product written in ', $d[2]);
    }

    public function testExecuteDirectory()
    {
        $command = $this->getCommand();

        $command->execute(['--directory' => 'Directory']);

        $d = explode(PHP_EOL, $command->getDisplay());

        $this->assertEquals('Processing bundle TestBundle', $d[0]);
        $this->assertRegexp('/^Directory .+ has been created/', $d[1]);
        $this->assertStringStartsWith('Schema for entity Person written in ', $d[2]);
    }

    public function testExecuteDirectoryShortcut()
    {
        $command = $this->getCommand();

        $command->execute(['-d' => 'Directory']);

        $d = explode(PHP_EOL, $command->getDisplay());

        $this->assertEquals('Processing bundle TestBundle', $d[0]);
        $this->assertRegexp('/^Directory .+ has been created/', $d[1]);
        $this->assertStringStartsWith('Schema for entity Person written in ', $d[2]);
    }

    public function testExecuteDoctrineStrategy()
    {
        $command = $this->getCommand();

        $command->execute(['--strategy' => 'doctrine']);

        $d = explode(PHP_EOL, $command->getDisplay());

        $this->assertEquals('Processing bundle TestBundle', $d[0]);
        $this->assertRegexp('/^Directory .+ has been created/', $d[1]);
        $this->assertStringStartsWith('Schema for entity Product written in ', $d[2]);
    }

    /**
     * @expectedException Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function testExecuteDoctrineStrategyDirectory()
    {
        $command = $this->getCommand();

        $command->execute(['--strategy' => 'doctrine', '-d' => 'Directory']);
    }

    public function testExecuteInvalidStrategy()
    {
        $command = $this->getCommand();
        $exitCode = $command->execute(['--strategy' => 'nonexistant', '-d' => 'Directory']);

        $d = explode(PHP_EOL, $command->getDisplay());

        $this->assertEquals($exitCode, 1);
        $this->assertEquals($d[0], 'Strategy must be one of: php, doctrine');
    }

    public function testMerge()
    {
        $command = $this->getCommand();
        $command->execute(['--strategy' => 'doctrine']);

        $path = $this->fixture.'/Product.json';
        $schema = json_decode(file_get_contents($path), true);

        $schema['properties']['name']['description'] = 'Some name description';
        $schema['properties']['description']['default'] = 'Default description';

        array_push($schema['required'], 'description');

        file_put_contents($path, json_encode($schema));

        $command->execute(['--strategy' => 'doctrine']);
        $this->assertEquals($schema, json_decode(file_get_contents($path), true));
    }

    public function tearDown()
    {
        $this->rmFixture();
    }
}
