<?php

namespace Drewlabs\Core\Hasher;

use Drewlabs\Contracts\Factory\IFactory as Factory;
use Drewlabs\Contracts\Hasher\IHasher as Hasher;

class HashFactory implements Factory
{

    /**
     * Hash instance
     * @var \Drewlabs\Contracts\Hasher\IHasher
     */
    private $hasher;

    /**
     * Make a new Factory class
     * @param string $type bcrypt|argon2
     * @return \Drewlabs\Contracts\Factory\IFactory
     */
    public function make($type = 'bcrypt'): Factory
    {
        switch (strtolower($type)) {
            case 'bcrypt':
                $this->hasher = new BCryptHasher();
                break;
            case 'argon2':
                $this->hasher = new Argon2Hasher();
                break;
            default:
                throw new \RuntimeException("Unimplemented hashing algorithm");
        }
        return $this;
    }
    /**
     * Resolve the constructed object
     * @return \Drewlabs\Contracts\Hasher\IHasher
     */
    public function resolve(): Hasher
    {
        return $this->hasher;
    }

    public function __destruct()
    {
        unset($this->hasher);
    }
}
