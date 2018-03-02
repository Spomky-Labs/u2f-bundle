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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
     * @var SessionInterface
     */
    private $session;

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
     * @param SessionInterface         $session
     * @param string                   $applicationId
     * @param array                    $issuerCertificates
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, SessionInterface $session, string $applicationId, array $issuerCertificates)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
        $this->applicationId = $applicationId;
        $this->issuerCertificates = $issuerCertificates;
    }

    /**
     * @return Response
     */
    public function getRegistrationRequestAction(): Response
    {
        try {
            $registrationRequest = RegistrationRequest::create($this->applicationId);
            $this->session->set('U2F_REGISTRATION_REQUEST', $registrationRequest);
            $this->eventDispatcher->dispatch(
                Events::U2F_REGISTRATION_REQUEST_ISSUED,
                new RegistrationRequestIssuedEvent($registrationRequest)
            );

            return new Response(
                json_encode($registrationRequest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                200,
                ['Content-Type' => 'application/json']
            );
        } catch (\Exception $e) {
            throw new HttpException(500, 'An error occurred during the creation of the registration request.', $e);
        }
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function postRegistrationRequestAction(Request $request): Response
    {
        $data = $request->getContent();
        if (null === $data) {
            throw new HttpException(400, 'The challenge response is missing');
        }

        $registrationRequest = $this->session->get('U2F_REGISTRATION_REQUEST');
        if (!$registrationRequest instanceof RegistrationRequest) {
            throw new HttpException(400, 'The registration request is missing');
        }
        $this->session->remove('U2F_REGISTRATION_REQUEST');

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

        return new Response('', 204);
    }
}
