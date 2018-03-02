<?php

namespace U2FAuthentication\Bundle\Model;

use U2FAuthentication\KeyHandle;

interface HasKeyCounters
{
    /**
     * @param KeyHandle $key
     *
     * @return int|null
     */
    public function getCounterForKey(KeyHandle $key): ?int;
}
