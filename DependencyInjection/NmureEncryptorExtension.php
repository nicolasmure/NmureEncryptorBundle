<?php

namespace Nmure\EncryptorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class NmureEncryptorExtension extends Extension
{
    /**
     * Indicates if the compilation of the container is required.
     * 
     * @var boolean
     */
    private $isCompilationRequired;

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['encryptors'] as $name => $settings) {
            $this->configureEncryptor($name, $settings, $container);
        }

        // resolving decorated services if needed
        if ($this->isCompilationRequired) {
            $container->compile();
        }
    }

    /**
     * @param  string           $name      Encryptor's name
     * @param  array            $settings  Encryptor's settings
     * @param  ContainerBuilder $container
     */
    private function configureEncryptor($name, array $settings, ContainerBuilder $container)
    {
        $serviceName = sprintf('nmure_encryptor.%s', $name);
        $container->register($serviceName, 'Nmure\EncryptorBundle\Encryptor\Encryptor')
            ->addArgument($settings['secret']);

        if ($settings['prefer_base64']) {
            $decoratorServiceName = sprintf('nmure_encryptor.adapter.base64.%s', $name);
            $container->register($decoratorServiceName, 'Nmure\EncryptorBundle\Adapter\Base64Adapter')
                ->addArgument(new Reference(sprintf('%s.inner', $decoratorServiceName)))
                ->setPublic(false)
                ->setDecoratedService($serviceName);

            $this->isCompilationRequired = true;
        }
    }
}
