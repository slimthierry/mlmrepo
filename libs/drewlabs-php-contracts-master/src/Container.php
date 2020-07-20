<?php

namespace Drewlabs\Contracts;

/**
 * Container is a key => value pair data structure implementation alowing to save
 * and retrieve values. It looks like a dictionnary
 *
 */
interface Container
{
    /**
     * Get an item from the container based on item $key
     *
     * @param string $item
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Check if container has a given value
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key) : bool;

    /**
     * Get all container values
     *
     * @return array|mixed
     */
    public function all();

    /**
     * Set a value to a container key
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function set($key, $value);
}
