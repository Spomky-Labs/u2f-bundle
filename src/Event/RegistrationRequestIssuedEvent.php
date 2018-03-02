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
use U2FAuthentication\RegistrationRequest;

class RegistrationRequestIssuedEvent extends Event
{
    /**
     * @var RegistrationRequest
     */
    private $registrationRequest;

    /**
     * RegistrationRequestIssuedEvent constructor.
     *
     * @param RegistrationRequest $registrationRequest
     */
    public function __construct(RegistrationRequest $registrationRequest)
    {
        $this->registrationRequest = $registrationRequest;
    }

    /**
     * @return RegistrationRequest
     */
    public function getRegistrationRequest(): RegistrationRequest
    {
        return $this->registrationRequest;
    }
}
