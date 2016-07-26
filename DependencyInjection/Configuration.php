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
                            ->booleanNode('turn_hex_key_to_bin')
                                ->info('Turns the hex secret key to a binary key')
                            ->end()
                            ->scalarNode('formatter')
                                ->info('The FormatterInterface to use with this Encryptor')
                            ->end()
                            ->booleanNode('disable_auto_iv_update')
                                ->info('Indicates if the automatic IV update is disabled')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
