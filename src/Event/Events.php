<?php

namespace U2FAuthentication\Bundle\Event;

class Events
{
    public const U2F_REGISTRATION_REQUEST_ISSUED = 'u2f_registration_request_issued';
    public const U2F_REGISTRATION_RESPONSE_INVALID = 'u2f_registration_response_invalid';
    public const U2F_REGISTRATION_RESPONSE_VALIDATED = 'u2f_registration_response_validated';

    public const U2F_SIGNATURE_REQUEST_ISSUED = 'u2f_signature_request_issued';
    public const U2F_SIGNATURE_RESPONSE_INVALID = 'u2f_signature_response_invalid';
    public const U2F_SIGNATURE_RESPONSE_VALIDATED = 'u2f_signature_response_validated';
}
