<?php

namespace Soyuka\JsonSchemaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class GenerateJsonSchemaCommand extends ContainerAwareCommand
{
    private $strategies = ['php', 'doctrine'];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('jsonschema:generate')
            ->setDescription('Generate json schemas from php files or doctrine entities')
            ->addOption(
                'directory',
                'd',
                InputOption::VALUE_REQUIRED,
                'The directory we should look into, inside each bundle',
                'Entity'
            )
            ->addOption(
                'strategy',
                null,
                InputOption::VALUE_REQUIRED,
                sprintf('Strategy to use, one of: %s', implode(', ', $this->strategies)),
                'php'
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> command generates schemas from doctrine entities. 
If a schema already exists for the provided entity, it'll be merged with the existing schema as a base (this will not override anything).

You can specify the directory where php files are available with the -d option:

  <info>php %command.full_name% -d Entity</info>

will generate json schema's on every php file in every bundle inside the "Entity" directory.
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        //look for every entity files
        $inside = $input->getOption('directory');
        $strategy = $input->getOption('strategy');

        if (!in_array($strategy, $this->strategies)) {
            $output->writeln(sprintf('<error>Strategy must be one of: %s', implode(', ', $this->strategies)));

            return 1;
        }

        $container = $this->getContainer();
        $schemaPath = $container->getParameter('json_schema.path');
        $strategy = $container->get(sprintf('json_schema.%s_strategy', $strategy));
        $bundles = $container->get('kernel')->getBundles();

        foreach ($bundles as $bundle) {
            if (!is_dir($dir = sprintf('%s/%s', $bundle->getPath(), $inside))) {
                continue;
            }

            $output->writeln(sprintf('<info>Processing bundle %s</info>', $bundle->getName()));

            $finder = new Finder();
            $finder->files()->name('*.php')->notName('*Repository.php')->in($dir);

            $prefix = $bundle->getNameSpace().'\\'.strtr($inside, '/', '\\');

            foreach ($finder as $file) {
                $ns = $prefix;

                $title = $file->getBasename('.php');
                $class = $ns.'\\'.$title;

                $directory = sprintf('%s/%s', $schemaPath, $bundle->getName());
                $schema = sprintf('%s/%s.json', $directory, $title);
                $existingSchema = null;

                if (!is_dir($directory)) {
                    mkdir($directory);
                    $output->writeln(sprintf('<info>Directory %s has been created</info>', $directory));
                }

                if (file_exists($schema)) {
                    $existingSchema = json_decode(file_get_contents($schema), true);
                }

                $jsonschema = $strategy->generate($class, ['title' => $title]);

                if ($existingSchema !== null) {
                    $jsonschema = $this->mergeRecursiveDistinct($existingSchema, $jsonschema);
                }

                file_put_contents($schema,
                    json_encode(
                        $jsonschema,
                        JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                    )
                );

                $output->writeln(sprintf('<info>Schema for entity %s written in %s</info>', $title, $schema));
            }
        }

        return 0;
    }

    /**
     * http://php.net/manual/fr/function.array-merge-recursive.php.
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     *
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     */
    private function mergeRecursiveDistinct(array &$array1, array &$array2): array
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->mergeRecursiveDistinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
