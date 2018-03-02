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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class U2FAuthenticationExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'u2f';
    }

    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('services.php');

        $container->setParameter('u2f.application_id', $mergedConfig['application_id']);
        $container->setParameter('u2f.issuer_certificates', $mergedConfig['issuer_certificates']);
    }
}
