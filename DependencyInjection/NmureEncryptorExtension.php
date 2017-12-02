<?php

namespace Nmure\EncryptorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
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
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

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

        $definition = $container->register(sprintf('nmure_encryptor.%s', $name), 'Nmure\Encryptor\Encryptor')
            ->addArgument($settings['secret'])
            ->addArgument($settings['cipher'])
            ->setPublic(true)
        ;

        if (isset($settings['formatter'])) {
            $this->configureFormatter($definition, $settings['formatter'], $container);
        }

        if (isset($settings['disable_auto_iv_update']) && $settings['disable_auto_iv_update']) {
            $definition->addMethodCall('disableAutoIvUpdate');
        }

        if (isset($settings['turn_hex_key_to_bin']) && $settings['turn_hex_key_to_bin']) {
            $definition->addMethodCall('turnHexKeyToBin');
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

    /**
     * Configure and set the formatter to the current encryptor.
     *
     * @param  Definition       $definition    Encryptor's service Definition
     * @param  string           $formatterName Formatter's service name
     * @param  ContainerBuilder $container
     *
     * @throws Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException When not able to find the formatter's service.
     */
    private function configureFormatter(Definition $definition, $formatterName, ContainerBuilder $container)
    {
        // throws an exception if the formatter's service does not exists
        $container->getDefinition($formatterName);

        $definition->addMethodCall('setFormatter', array(new Reference($formatterName)));
    }
}
