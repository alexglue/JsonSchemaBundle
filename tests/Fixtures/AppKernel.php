<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Soyuka\JsonSchemaBundle\JsonSchemaBundle;
use Soyuka\JsonSchemaBundle\Tests\Fixtures\TestBundle\TestBundle;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [];

        if (in_array($this->getEnvironment(), ['test', 'testbis'])) {
            $bundles[] = new FrameworkBundle();
            $bundles[] = new DoctrineBundle();
            $bundles[] = new JsonSchemaBundle();
            $bundles[] = new TestBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(sprintf('%s/config_%s.yml', __DIR__, $this->getEnvironment()));
    }
}
