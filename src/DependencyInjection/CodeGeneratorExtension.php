<?php

namespace Bundles\CodeGeneratorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CodeGeneratorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources'));
        $loader->load('services/command.yaml');
        $loader->load('services/controller.yaml');
        $loader->load('services/form.yaml');
        $loader->load('services/repository.yaml');
    }
}
