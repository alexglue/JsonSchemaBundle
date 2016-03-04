<?php

namespace Soyuka\JsonSchemaBundle\Tests;

use Doctrine\Common\Annotations\AnnotationRegistry;

trait KernelTrait
{
    private $kernel;

    public function boot(string $env = 'test')
    {
        AnnotationRegistry::registerFile(__DIR__.'/../src/Constraints/JsonSchema.php');
        require_once __DIR__.'/Fixtures/AppKernel.php';
        $this->kernel = new \AppKernel($env, true);
        $this->kernel->boot();
    }

    public function getContainer()
    {
        return $this->kernel->getContainer();
    }
}
