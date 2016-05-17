<?php

namespace Nmure\EncryptorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class NmureEncryptorExtension extends Extension
{
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
    }

    /**
     * @param  string           $name      Encryptor's name
     * @param  array            $settings  Encryptor's settings
     * @param  ContainerBuilder $container
     */
    private function configureEncryptor($name, array $settings, ContainerBuilder $container)
    {
        $this->assertSupportedCipher($settings['cipher']);

        $serviceName = sprintf('nmure_encryptor.%s', $name);
        $container->register($serviceName, 'Nmure\EncryptorBundle\Encryptor\Encryptor')
            ->addArgument($settings['secret'])
            ->addArgument($settings['cipher']);

        if ($settings['prefer_base64']) {
            $decoratorServiceName = sprintf('nmure_encryptor.adapter.base64.%s', $name);
            $container->register($decoratorServiceName, 'Nmure\EncryptorBundle\Adapter\Base64Adapter')
                ->addArgument(new Reference(sprintf('%s.inner', $decoratorServiceName)))
                ->setPublic(false)
                ->setDecoratedService($serviceName);
        }
    }

    /**
     * Asserts the given cipher is supported.
     * 
     * @param  string $cipher
     * 
     * @throws InvalidConfigurationException When the given cipher is not supported.
     */
    private function assertSupportedCipher($cipher)
    {
        $supportedCiphers = openssl_get_cipher_methods();
        if (!in_array($cipher, $supportedCiphers)) {
            throw new InvalidConfigurationException(sprintf(
                '%s cipher method is not supported. The supported cipher methods by your php installation are %s .',
                $cipher,
                implode(', ', $supportedCiphers)
            ));
        }
    }
}
