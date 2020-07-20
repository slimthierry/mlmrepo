<?php

namespace Drewlabs\Core\Hasher;

class HasherBase
{
    /**
     * @var int
     */
    protected $rounds = 10;

    /**
     * Makes a hashed value based on a string
     * @param string $value
     * @param string $algo
     * @param array
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function hash($value, $algo, $options = array())
    {
        $hashed_value = password_hash($value, $algo, $options);
        if ($hashed_value) {
            return $hashed_value;
        }
        throw new \RuntimeException("$algo hashing algorithm is not supported");
    }

    /**
     * Check hashed value against a given string
     * @param string $value
     * @param string $hashed_value
     * @param array $options
     * @return bool
     */
    protected function hashCompare($value, $hashed_value, $options = array()): bool
    {
        return (isset($hashed_value) || (!empty($hashed_value))) ? password_verify($value, $hashed_value) : false;
    }

    /**
     * Verify if hashed_value has been compute using a given options
     * @param string $hashed_value
     * @param string $algo
     * @param array $options
     */
    protected function passwordNeedsRehash($hashed_value, $algo, array $options = array()): bool
    {
        return password_needs_rehash($hashed_value, $algo, $options);
    }
}
