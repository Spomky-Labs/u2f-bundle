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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use U2FAuthentication\Bundle\Event\Events;
use U2FAuthentication\Bundle\Event\SignatureRequestIssuedEvent;
use U2FAuthentication\Bundle\Event\SignatureResponseInvalidEvent;
use U2FAuthentication\Bundle\Event\SignatureResponseValidatedEvent;
use U2FAuthentication\Bundle\Model\HasKeyCounters;
use U2FAuthentication\Bundle\Model\HasRegisteredKeys;
use U2FAuthentication\KeyHandle;
use U2FAuthentication\SignatureRequest;
use U2FAuthentication\SignatureResponse;

class SignatureController
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * SignatureRequestController constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param SessionInterface         $session
     * @param string                   $applicationId
     * @param TokenStorageInterface    $tokenStorage
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, SessionInterface $session, string $applicationId, TokenStorageInterface $tokenStorage)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
        $this->applicationId = $applicationId;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return Response
     */
    public function getSignatureRequestAction(): Response
    {
        $user = $this->getUser();

        try {
            $signatureRequest = SignatureRequest::create(
                $this->applicationId,
                $user->getRegisteredKeys()
            );
            $this->session->set('U2F_SIGNATURE_REQUEST', $signatureRequest);
            $this->eventDispatcher->dispatch(
                Events::U2F_SIGNATURE_REQUEST_ISSUED,
                new SignatureRequestIssuedEvent($user, $signatureRequest)
            );

            return new Response(
                json_encode($signatureRequest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                200,
                ['Content-Type' => 'application/json']
            );
        } catch (\Exception $e) {
            throw new HttpException(500, 'An error occurred during the creation of the signature request.', $e);
        }
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function postSignatureRequestAction(Request $request): Response
    {
        $user = $this->getUser();
        $data = $request->getContent();
        if (null === $data) {
            throw new HttpException(400, 'The challenge response is missing');
        }

        $signatureRequest = $this->session->get('U2F_SIGNATURE_REQUEST');
        if (!$signatureRequest instanceof SignatureRequest) {
            throw new HttpException(400, 'The signature request is missing');
        }
        $this->session->remove('U2F_SIGNATURE_REQUEST');

        $signatureResponse = SignatureResponse::create($data);
        $counter = $this->getKeyCounter($signatureResponse->getKeyHandle(), $user);

        if (!$signatureResponse->isValid($signatureRequest, $counter)) {
            $this->eventDispatcher->dispatch(
                Events::U2F_REGISTRATION_RESPONSE_INVALID,
                new SignatureResponseInvalidEvent($user, $signatureResponse)
            );

            throw new HttpException(400, 'The signature response is invalid');
        }

        $this->eventDispatcher->dispatch(
            Events::U2F_REGISTRATION_RESPONSE_VALIDATED,
            new SignatureResponseValidatedEvent($user, $signatureResponse)
        );

        return new Response('', 204);
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

    /**
     * @param KeyHandle         $keyHandle
     * @param HasRegisteredKeys $user
     *
     * @return int|null
     */
    private function getKeyCounter(KeyHandle $keyHandle, HasRegisteredKeys $user): ?int
    {
        if (!$user instanceof HasKeyCounters) {
            return null;
        }

        return $user->getCounterForKey($keyHandle);
    }
}
