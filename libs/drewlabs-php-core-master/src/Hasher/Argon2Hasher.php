<?php

namespace Drewlabs\Core\Hasher;

use Drewlabs\Contracts\Hasher\IHasher;

class Argon2Hasher extends HasherBase implements IHasher
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
        $options['time_cost'] = $this->cost($options) ? $this->cost($options) : PASSWORD_ARGON2_DEFAULT_TIME_COST;
        $options['memory_cost'] = isset($options['memory_cost']) ? $options['memory_cost'] : PASSWORD_ARGON2_DEFAULT_MEMORY_COST;
        $options['threads'] = isset($options['threads']) ? $options['threads'] : PASSWORD_ARGON2_DEFAULT_THREADS;
        return $this->hash($value, PASSWORD_ARGON2I, $options);
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
        return $this->hashCompare($value, $hashed_value, $options);
    }

    /**
     * Check if password has been hashed with given options
     * @param string $hashed_value
     * @param array $options
     * @return bool
     */
    public function needsRehash($hashed_value, $options): bool
    {
        $options['cost'] = $this->cost($options);
        return $this->passwordNeedsRehash($hashed_value, PASSWORD_ARGON2I, $options);
    }

    public function setRounds($rounds)
    {
        $this->rounds = (int) $rounds;
        return $this;
    }

    /**
     * Extract the cost value from the options array.
     * @param  array  $options
     * @return int
     */
    protected function cost(array $options = [])
    {
        return $options['rounds'] ?? $this->rounds;
    }
}
