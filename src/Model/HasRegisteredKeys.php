<?php

namespace U2FAuthentication\Bundle\Model;

use U2FAuthentication\RegisteredKey;

interface HasRegisteredKeys
{
    /**
     * @return RegisteredKey[]
     */
    public function getRegisteredKeys(): array;
}
