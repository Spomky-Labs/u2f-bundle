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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use U2FAuthentication\Bundle\Event\Events;
use U2FAuthentication\Bundle\Event\RegistrationRequestIssuedEvent;
use U2FAuthentication\Bundle\Event\RegistrationResponseInvalidEvent;
use U2FAuthentication\Bundle\Event\RegistrationResponseValidatedEvent;
use U2FAuthentication\Bundle\Model\HasRegisteredKeys;
use U2FAuthentication\RegistrationRequest;
use U2FAuthentication\RegistrationResponse;

class RegistrationController
{
    /**
     * @var string
     */
    private $applicationId;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var array
     */
    private $issuerCertificates;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * RegistrationController constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param TokenStorageInterface    $tokenStorage
     * @param string                   $applicationId
     * @param array                    $issuerCertificates
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, TokenStorageInterface $tokenStorage, string $applicationId, array $issuerCertificates)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenStorage = $tokenStorage;
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
        $user = $this->getUser();

        try {
            $registrationRequest = RegistrationRequest::create($this->applicationId, $user->getRegisteredKeys());
            $request->getSession()->set('U2F_REGISTRATION_REQUEST', $registrationRequest);
            $this->eventDispatcher->dispatch(
                Events::U2F_REGISTRATION_REQUEST_ISSUED,
                new RegistrationRequestIssuedEvent($user, $registrationRequest)
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
        $user = $this->getUser();
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
                new RegistrationResponseInvalidEvent($user, $registrationResponse)
            );

            throw new HttpException(400, 'The registration response is invalid');
        }

        $this->eventDispatcher->dispatch(
            Events::U2F_REGISTRATION_RESPONSE_VALIDATED,
            new RegistrationResponseValidatedEvent($user, $registrationResponse)
        );

        return new JsonResponse(['registered' => 'ok'], 204);
    }

    /**
     * @return HasRegisteredKeys
     */
    private function getUser(): HasRegisteredKeys
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            throw new AccessDeniedHttpException('The user must be connected');
        }
        $user = $token->getUser();
        if (null === $user) {
            throw new AccessDeniedHttpException('The user must be connected');
        }
        if (!$user instanceof HasRegisteredKeys) {
            throw new AccessDeniedHttpException('The user does not support the U2F keys');
        }

        return $user;
    }
}
