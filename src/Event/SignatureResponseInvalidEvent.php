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
use U2FAuthentication\SignatureResponse;

class SignatureResponseInvalidEvent extends Event
{
    /**
     * @var HasRegisteredKeys
     */
    private $user;
    /**
     * @var SignatureResponse
     */
    private $signatureResponse;

    /**
     * SignatureResponseInvalidEvent constructor.
     *
     * @param HasRegisteredKeys $user
     * @param SignatureResponse $signatureResponse
     */
    public function __construct(HasRegisteredKeys $user, SignatureResponse $signatureResponse)
    {
        $this->user = $user;
        $this->signatureResponse = $signatureResponse;
    }

    /**
     * @return HasRegisteredKeys
     */
    public function getUser(): HasRegisteredKeys
    {
        return $this->user;
    }

    /**
     * @return SignatureResponse
     */
    public function getSignatureResponse(): SignatureResponse
    {
        return $this->signatureResponse;
    }
}
