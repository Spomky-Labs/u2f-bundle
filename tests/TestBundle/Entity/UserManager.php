<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace U2FAuthentication\Bundle\Tests\TestBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class UserManager
{
    public function isPasswordCredentialValid(UserInterface $user, string $password): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        return in_array($password, $user->getOAuth2Passwords());
    }
}
