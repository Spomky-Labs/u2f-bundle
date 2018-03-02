<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace U2FAuthentication\Bundle\Controller;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use U2FAuthentication\Bundle\Event\Events;
use U2FAuthentication\Bundle\Event\RegistrationRequestIssuedEvent;
use U2FAuthentication\Bundle\Event\RegistrationResponseInvalidEvent;
use U2FAuthentication\Bundle\Event\RegistrationResponseValidatedEvent;
use U2FAuthentication\RegistrationRequest;
use U2FAuthentication\RegistrationResponse;

class RegistrationController
{
    /**
     * @var string
     */
    private $applicationId;

    /**
     * @var array
     */
    private $issuerCertificates;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * RegistrationRequestController constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $applicationId
     * @param array                    $issuerCertificates
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, string $applicationId, array $issuerCertificates)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->applicationId = $applicationId;
        $this->issuerCertificates = $issuerCertificates;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getRegistrationRequestAction(Request $request): JsonResponse
    {
        try {
            $registrationRequest = RegistrationRequest::create($this->applicationId);
            $request->getSession()->set('U2F_REGISTRATION_REQUEST', $registrationRequest);
            $this->eventDispatcher->dispatch(
                Events::U2F_REGISTRATION_REQUEST_ISSUED,
                new RegistrationRequestIssuedEvent($registrationRequest)
            );

            return new JsonResponse($registrationRequest);
        } catch (\Exception $e) {
            throw new HttpException(500, 'An error occurred during the creation of the registration request.', $e);
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postRegistrationRequestAction(Request $request): JsonResponse
    {
        $registrationRequest = $request->getSession()->get('U2F_REGISTRATION_REQUEST');
        if (!$registrationRequest instanceof RegistrationRequest) {
            throw new HttpException(400, 'The registration request is missing');
        }
        $request->getSession()->remove('U2F_REGISTRATION_REQUEST');

        $data = $request->request->all();
        $registrationResponse = RegistrationResponse::create($data);

        if (!$registrationResponse->isValid($registrationRequest, $this->issuerCertificates)) {
            $this->eventDispatcher->dispatch(
                Events::U2F_REGISTRATION_RESPONSE_INVALID,
                new RegistrationResponseInvalidEvent($registrationResponse)
            );

            throw new HttpException(400, 'The registration response is invalid');
        }

        $this->eventDispatcher->dispatch(
            Events::U2F_REGISTRATION_RESPONSE_VALIDATED,
            new RegistrationResponseValidatedEvent($registrationResponse)
        );

        return new JsonResponse(['registered' => 'ok'], 204);
    }
}
