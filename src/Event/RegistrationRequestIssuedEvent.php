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
use U2FAuthentication\RegistrationRequest;

class RegistrationRequestIssuedEvent extends Event
{
    /**
     * @var HasRegisteredKeys
     */
    private $user;

    /**
     * @var RegistrationRequest
     */
    private $registrationRequest;

    /**
     * RegistrationRequestIssuedEvent constructor.
     *
     * @param HasRegisteredKeys   $user
     * @param RegistrationRequest $registrationRequest
     */
    public function __construct(HasRegisteredKeys $user, RegistrationRequest $registrationRequest)
    {
        $this->user = $user;
        $this->registrationRequest = $registrationRequest;
    }

    /**
     * @return HasRegisteredKeys
     */
    public function getUser(): HasRegisteredKeys
    {
        return $this->user;
    }

    /**
     * @return RegistrationRequest
     */
    public function getRegistrationRequest(): RegistrationRequest
    {
        return $this->registrationRequest;
    }
}
