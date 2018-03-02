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

namespace U2FAuthentication\Bundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @group Bundle
 * @group Functional
 */
class SignatureRequestTest extends WebTestCase
{
    /**
     * @test
     */
    public function theSignatureRequestEndpointIsAvailableAndReturnsAJsonObject()
    {
        $client = static::createClient();

        $client->request('GET', '/2fa/u2f/sign', [], [], ['HTTPS' => 'on', 'HTTP_AUTHORIZATION' => 'Basic '.base64_encode('john.1:secret')]);
        $response = $client->getResponse();

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('application/json', $response->headers->get('Content-Type'));

        $container = $client->getContainer();
        /** @var SessionInterface $session */
        $session = $container->get('session');
        self::assertTrue($session->has('U2F_SIGNATURE_REQUEST'));
    }
}
