<?php

namespace Drewlabs\Contracts\Hasher;

interface IHasher
{
    /**
     * Generate a hash value from a given string
     * @param string $value
     * @param array $options
     * @return string
     */
    public function make($value, array $options = array());

    /**
     * Check if computed hash match a given value
     * @param string $value
     * @param string $hashed_value
     * @param array $options
     * @return bool
     */
    public function check($value, $hashed_value, array $options = array()): bool;

    /**
     * Check if the given hash value has been hashed with given options
     * @param string $hashed_value
     * @param array $options
     * @return bool
     */
    public function needsRehash($hashed_value, $options): bool;
}
