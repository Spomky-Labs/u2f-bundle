<?php

namespace U2FAuthentication\Bundle\Event;

use Symfony\Component\EventDispatcher\Event;
use U2FAuthentication\RegistrationResponse;

class RegistrationResponseValidatedEvent extends Event
{
    /**
     * @var RegistrationResponse
     */
    private $registrationResponse;

    /**
     * RegistrationResponseIssuedEvent constructor.
     *
     * @param RegistrationResponse $registrationResponse
     */
    public function __construct(RegistrationResponse $registrationResponse)
    {
        $this->registrationResponse = $registrationResponse;
    }

    /**
     * @return RegistrationResponse
     */
    public function getRegistrationResponse(): RegistrationResponse
    {
        return $this->registrationResponse;
    }
}
