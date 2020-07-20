<?php

namespace Drewlabs\Core\Storage;

use Drewlabs\Contracts\Storage\IStorage;

class ArrayStorage implements IStorage
{
    /**
     * Array of stored values.
     *
     * @var array
     */
    protected $store;

    public function __construct()
    {
        $this->store = [];
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string|array  $key
     * @return mixed
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->storage)) {
            return $this->storage[$key];
        }
    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  float|int  $minutes
     * @return void
     */
    public function put($key, $value, $minutes)
    {
        $this->storage[$key] = $value;
    }

    /**
     * Update a value in the store
     *
     * @param  string  $key
     * @param  mixed   $value
     */
    public function update($key, $value)
    {
        $this->storage[$key] = !isset($this->storage[$key]) ? $value : $value;
        return $this->storage[$key];
    }

    /**
     * Check if a value exists in the storage
     *
     * @param string $key
     * @return boolean
     *
     */
    public function has($key): bool
    {
        return array_key_exists($key, $this->storage);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function delete($key): bool
    {
        unset($this->storage[$key]);
        return true;
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush(): bool
    {
        $this->storage = [];
        return true;
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix(): ?string
    {
        return '';
    }
}
