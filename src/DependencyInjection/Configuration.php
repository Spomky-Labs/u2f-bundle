<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace U2FAuthentication\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('u2f');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('application_id')
                    ->info('The application ID')
                    ->isRequired()
                ->end()
                ->arrayNode('issuer_certificates')
                    ->info('List of paths to certificate files. If set, the registered keys must have been manufactured by the issuer of the certificates')
                    ->treatNullLike([])
                    ->treatFalseLike([])
                    ->scalarPrototype()->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
