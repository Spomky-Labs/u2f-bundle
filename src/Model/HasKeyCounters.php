<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

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
