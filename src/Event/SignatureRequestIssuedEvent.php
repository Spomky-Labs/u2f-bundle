<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace U2FAuthentication\Bundle\Event;

use Symfony\Component\EventDispatcher\Event;
use U2FAuthentication\Bundle\Model\HasRegisteredKeys;
use U2FAuthentication\SignatureRequest;

class SignatureRequestIssuedEvent extends Event
{
    /**
     * @var HasRegisteredKeys
     */
    private $user;

    /**
     * @var SignatureRequest
     */
    private $signatureRequest;

    /**
     * SignatureRequestIssuedEvent constructor.
     *
     * @param HasRegisteredKeys $user
     * @param SignatureRequest  $signatureRequest
     */
    public function __construct(HasRegisteredKeys $user, SignatureRequest $signatureRequest)
    {
        $this->user = $user;
        $this->signatureRequest = $signatureRequest;
    }

    /**
     * @return HasRegisteredKeys
     */
    public function getUser(): HasRegisteredKeys
    {
        return $this->user;
    }

    /**
     * @return SignatureRequest
     */
    public function getSignatureRequest(): SignatureRequest
    {
        return $this->signatureRequest;
    }
}
