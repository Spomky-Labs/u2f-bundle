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
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use U2FAuthentication\RegistrationRequest;
use U2FAuthentication\RegistrationResponse;

/**
 * @group Bundle
 * @group Functional
 */
class RegistrationResponseTest extends WebTestCase
{
    /**
     * @test
     */
    public function theRegistrationResponseEndpointIsAvailableAndCanValidateTokenResponses()
    {
        $client = static::createClient();

        $registrationResponse = $this->prophesize(RegistrationRequest::class);
        $registrationResponse->getChallenge()->willReturn(Base64Url::decode('3lp3lcuYSHo3yrGfuLvQ5NEd-LWDTHRVaDIKXfBvh8s'));
        $registrationResponse->getApplicationId()->willReturn('https://twofactors:4043');
        $container = $client->getContainer();
        /** @var SessionInterface $session */
        $session = $container->get('session');
        $session->set('U2F_REGISTRATION_REQUEST', $registrationResponse->reveal());

        $client->request(
            'POST',
            '/2fa/u2f/register',
            ['data' => '{"registrationData":"BQRXlpZUqJXsfHYh7MCh1us-yprazMqTJyjWXocA0j_UT59xmzHgf1mkT9Dr82nGtYPX396PrUQx4_54haTZo8g6QFrNackWqHMDTcWCFyB7bYzjtetvJFUNhCuhFVWf8FirNPPfq7M09n5Ep0n5uFesFfa8s9vivFZuRbP6-3LQHKEwggItMIIBF6ADAgECAgQFtgV5MAsGCSqGSIb3DQEBCzAuMSwwKgYDVQQDEyNZdWJpY28gVTJGIFJvb3QgQ0EgU2VyaWFsIDQ1NzIwMDYzMTAgFw0xNDA4MDEwMDAwMDBaGA8yMDUwMDkwNDAwMDAwMFowKDEmMCQGA1UEAwwdWXViaWNvIFUyRiBFRSBTZXJpYWwgOTU4MTUwMzMwWTATBgcqhkjOPQIBBggqhkjOPQMBBwNCAAT9uN6zoe1w62NsBm62AGmWpflw_LXbiPw7MF1B5ZZvDBtUuFL-8KCQftF_O__CnU0yG5z4qEos6qA4yr011ZjeoyYwJDAiBgkrBgEEAYLECgIEFTEuMy42LjEuNC4xLjQxNDgyLjEuMTALBgkqhkiG9w0BAQsDggEBAH7T-2zMJSAT-C8hjCo32mAx0g5_MIHa_K6xKPx_myM5FL-2TWE18XziIfp2T0U-8Sc6jOlllWRCuy8eR0g_c33LyYtYU3f-9QsnDgKJ-IQ28a3PSbJiHuXjAt9VW5q3QnLgafkYFJs97E8SIosQwPiN42r1inS7RCuFrgBTZL2mcCBY_B8th5tTARHqYOhsY_F_pZRMyD8KommEiz7jiKbAnmsFlT_LuPR-g6J-AHKmPDKtZIZOkm1xEvoZl_eDllb7syvo94idDwFFUZonr92ORrBMpCkNhUC2NLiGFh51iMhimdzdZDXRZ4o6bwp0gpxN0_cMNSTR3fFteK3SG2QwRAIgDh1xe2NkrGHygQQsdbUbsIDo5rzK98uGFdtRnnkAcMECIAueb-X0G1j67XwU3JRd8_9bAJiFBnzTxvTWifRUtiUm","version":"U2F_V2","challenge":"3lp3lcuYSHo3yrGfuLvQ5NEd-LWDTHRVaDIKXfBvh8s","clientData":"eyJ0eXAiOiJuYXZpZ2F0b3IuaWQuZmluaXNoRW5yb2xsbWVudCIsImNoYWxsZW5nZSI6IjNscDNsY3VZU0hvM3lyR2Z1THZRNU5FZC1MV0RUSFJWYURJS1hmQnZoOHMiLCJvcmlnaW4iOiJodHRwczovL3R3b2ZhY3RvcnM6NDA0MyIsImNpZF9wdWJrZXkiOiJ1bnVzZWQifQ"}'],
            [],
            ['HTTPS' => 'on']
        );
        $response = $client->getResponse();

        self::assertEquals(204, $response->getStatusCode());
        self::assertEquals('', $response->getContent());
        self::assertFalse($session->has('U2F_REGISTRATION_REQUEST'));
    }
}
