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
use U2FAuthentication\RegistrationResponse;

class RegistrationResponseValidatedEvent extends Event
{
    /**
     * @var HasRegisteredKeys
     */
    private $user;

    /**
     * @var RegistrationResponse
     */
    private $registrationResponse;

    /**
     * RegistrationResponseIssuedEvent constructor.
     *
     * @param HasRegisteredKeys    $user
     * @param RegistrationResponse $registrationResponse
     */
    public function __construct(HasRegisteredKeys $user, RegistrationResponse $registrationResponse)
    {
        $this->user = $user;
        $this->registrationResponse = $registrationResponse;
    }

    /**
     * @return HasRegisteredKeys
     */
    public function getUser(): HasRegisteredKeys
    {
        return $this->user;
    }

    /**
     * @return RegistrationResponse
     */
    public function getRegistrationResponse(): RegistrationResponse
    {
        return $this->registrationResponse;
    }
}
