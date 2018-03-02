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

use Base64Url\Base64Url;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use U2FAuthentication\KeyHandle;
use U2FAuthentication\PublicKey;
use U2FAuthentication\RegisteredKey;
use U2FAuthentication\SignatureRequest;

/**
 * @group Bundle
 * @group Functional
 */
class SignatureResponseTest extends WebTestCase
{
    /**
     * @test
     */
    public function theSignatureResponseEndpointIsAvailableAndCanValidateTokenResponses()
    {
        $client = static::createClient();

        $signatureRequest = $this->prophesize(SignatureRequest::class);
        $signatureRequest->getChallenge()->willReturn(Base64Url::decode('F-zksRh5thzKyZR6O0Fr7QxlZ-xEX9_mNH8H3cHn_Po'));
        $signatureRequest->getApplicationId()->willReturn('https://twofactors:4043');
        $signatureRequest->hasRegisteredKey(Argument::type(KeyHandle::class))->willReturn(true);
        $signatureRequest->getRegisteredKey(Argument::type(KeyHandle::class))->willReturn(
            RegisteredKey::create(
                'U2F_V2',
                KeyHandle::create(Base64Url::decode('Ws1pyRaocwNNxYIXIHttjOO1628kVQ2EK6EVVZ_wWKs089-rszT2fkSnSfm4V6wV9ryz2-K8Vm5Fs_r7ctAcoQ')),
                PublicKey::create(Base64Url::decode('BFeWllSolex8diHswKHW6z7KmtrMypMnKNZehwDSP9RPn3GbMeB_WaRP0Ovzaca1g9ff3o-tRDHj_niFpNmjyDo')),
                '-----BEGIN PUBLIC KEY-----'.PHP_EOL.
                'MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEV5aWVKiV7Hx2IezAodbrPsqa2szK'.PHP_EOL.
                'kyco1l6HANI/1E+fcZsx4H9ZpE/Q6/NpxrWD19/ej61EMeP+eIWk2aPIOg=='.PHP_EOL.
                '-----END PUBLIC KEY-----'.PHP_EOL
            )
        );

        $container = $client->getContainer();
        /** @var SessionInterface $session */
        $session = $container->get('session');
        $session->set('U2F_SIGNATURE_REQUEST', $signatureRequest->reveal());

        $client->request(
            'POST',
            '/2fa/u2f/sign',
            [
                'keyHandle'     => 'Ws1pyRaocwNNxYIXIHttjOO1628kVQ2EK6EVVZ_wWKs089-rszT2fkSnSfm4V6wV9ryz2-K8Vm5Fs_r7ctAcoQ',
                'clientData'    => 'eyJ0eXAiOiJuYXZpZ2F0b3IuaWQuZ2V0QXNzZXJ0aW9uIiwiY2hhbGxlbmdlIjoiRi16a3NSaDV0aHpLeVpSNk8wRnI3UXhsWi14RVg5X21OSDhIM2NIbl9QbyIsIm9yaWdpbiI6Imh0dHBzOi8vdHdvZmFjdG9yczo0MDQzIiwiY2lkX3B1YmtleSI6InVudXNlZCJ9',
                'signatureData' => 'AQAAALowRQIgU-oyzSNitffUGZgRSEijbBytbz8ZwxZvnKSVC90oAm8CIQDoMW5ZtwUooptNB5M-2W_jSjT0yNOkWnU_w1e9aj7vMA',
            ],
            [],
            ['HTTPS' => 'on', 'HTTP_AUTHORIZATION' => 'Basic '.base64_encode('john.1:secret')]
        );
        $response = $client->getResponse();

        self::assertEquals(204, $response->getStatusCode());
        self::assertEquals('', $response->getContent());

        self::assertFalse($session->has('U2F_SIGNATURE_REQUEST'));
    }
}
