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
use U2FAuthentication\Bundle\Controller\RegistrationController;
use U2FAuthentication\Bundle\Controller\SignatureController;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return function (ContainerConfigurator $container) {
    $container = $container->services()->defaults()
        ->private()
        ->autoconfigure()
        ->autowire();

    $container->set(RegistrationController::class)
        ->args([
            ref('event_dispatcher'),
            ref('security.token_storage'),
            '%u2f.application_id%',
            '%u2f.issuer_certificates%',
        ])
        ->tag('controller.service_arguments');

    $container->set(SignatureController::class)
        ->args([
            ref('event_dispatcher'),
            '%u2f.application_id%',
            ref('security.token_storage'),
        ])
        ->tag('controller.service_arguments');
};
