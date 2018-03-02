<?php

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace U2FAuthentication\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use U2FAuthentication\Bundle\DependencyInjection\U2FAuthenticationExtension;

class U2FAuthenticationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new U2FAuthenticationExtension();
    }
}
