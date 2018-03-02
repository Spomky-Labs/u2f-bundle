<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use U2FAuthentication\Bundle\Tests\TestBundle\Entity\UserManager;
use U2FAuthentication\Bundle\Tests\TestBundle\Entity\UserRepository;
use U2FAuthentication\Bundle\Tests\TestBundle\Service\UserProvider;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return function (ContainerConfigurator $container) {
    $container = $container->services()->defaults()
        ->public()
        ->autoconfigure();

    $container->set(UserManager::class)
        ->class(UserManager::class);

    $container->set(UserRepository::class)
        ->class(UserRepository::class);

    $container->set(UserProvider::class)
        ->args([
            ref(UserRepository::class),
        ]);
};
