<?php

namespace Drewlabs\Core\Hasher;

use Drewlabs\Contracts\Hasher\IHasher;

class MD5Hasher implements IHasher
{

 /**
  * Hash a given string with or without options
  * @param string $value
  * @param array $options
  * @return string
  *
  * @throws \RuntimeException
  */
    public function make($value, array $options = array()): ?string
    {
        return md5($value);
    }

    /**
     * Check a string against a hashed value
     * @param string $value
     * @param string $hashed_value
     * @param array $options
     * @return bool
     */
    public function check($value, $hashed_value, array $options = array()): bool
    {
        return strcmp(md5($value), $hashed_value) === 0 ? true : false;
    }

    /**
     * Check if password has been hashed with given options
     * @param string $hashed_value
     * @param array $options
     * @return bool
     */
    public function needsRehash($hashed_value, $options): bool
    {
        return true;
    }
}
