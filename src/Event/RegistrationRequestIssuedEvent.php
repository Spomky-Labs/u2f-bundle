<?php

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
