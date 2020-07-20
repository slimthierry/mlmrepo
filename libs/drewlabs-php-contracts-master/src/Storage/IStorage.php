<?php

namespace Drewlabs\Contracts\Storage;

interface IStorage
{
    /**
     * Resolve a given value that match the key from the storage
     *
     * @param string $key
     * @return serialized
     *
     */
    public function get($key);

    /**
     * Add new value to the storage
     *
     * @param string $key
     * @return void
     *
     */
    public function put($key, $value, $expiration = null);

    /**
     * Removes a value from the storage
     *
     * @param string $key
     * @return boolean
     *
     */
    public function delete($key): bool;

    /**
     * Check if a value exists in the storage
     *
     * @param string $key
     * @return boolean
     *
     */
    public function has($key): bool;

    /**
     * Clear all values from the storage
     *
     * @return void
     */
    public function flush();
}
