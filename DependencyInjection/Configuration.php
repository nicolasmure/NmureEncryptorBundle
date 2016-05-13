<?php

namespace Nmure\EncryptorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nmure_encryptor');

        $rootNode
            ->children()
                ->arrayNode('encryptors')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('secret')
                                ->info('The encryption key')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('cipher')
                                ->info('The cipher method')
                                ->defaultValue('AES-256-CBC')
                            ->end()
                            ->integerNode('iv_length')
                                ->info('The length of the Initialization Vector, in number of bytes.')
                                ->defaultValue(16)
                            ->end()
                            ->booleanNode('prefer_base64')
                                ->info('Indicates if the encrypted data should be converted to base64')
                                ->defaultTrue()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
