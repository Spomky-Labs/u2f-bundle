<?php

declare(strict_types=1);

/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2018 Spomky-Labs
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace U2FAuthentication\Bundle\Tests\TestBundle\Entity;

use Base64Url\Base64Url;
use Symfony\Component\Security\Core\User\UserInterface;
use U2FAuthentication\Bundle\Model\HasKeyCounters;
use U2FAuthentication\Bundle\Model\HasRegisteredKeys;
use U2FAuthentication\KeyHandle;
use U2FAuthentication\RegisteredKey;

class User implements UserInterface, HasRegisteredKeys, HasKeyCounters
{
    /**
     * @var string
     */
    private $username;

    /**
     * User constructor.
     *
     * @param string          $username
     * @param RegisteredKey[] $registeredKeys
     * @param int[]           $keyCounters
     */
    public function __construct(string $username, array $registeredKeys, array $keyCounters)
    {
        $this->username = $username;
        $this->registeredKeys = $registeredKeys;
        $this->keyCounters = $keyCounters;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): string
    {
        return 'secret';
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * @var int[]
     */
    private $keyCounters = [];

    /**
     * {@inheritdoc}
     */
    public function getCounterForKey(KeyHandle $key): ?int
    {
        $b64 = Base64Url::encode($key->getValue());
        if (!array_key_exists($b64, $this->keyCounters)) {
            return null;
        }

        return $this->keyCounters[$b64];
    }

    /**
     * @var RegisteredKey[]
     */
    private $registeredKeys = [];

    /**
     * {@inheritdoc}
     */
    public function getRegisteredKeys(): array
    {
        return $this->registeredKeys;
    }
}
