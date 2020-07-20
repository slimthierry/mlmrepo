<?php

namespace Drewlabs\Utils\Cache;

use Drewlabs\Contracts\Storage\IStorage;
use Illuminate\Contracts\Container\Container;

class CacheStorage implements IStorage
{
    /**
     * Illuminate cache manager
     *
     * @var mixed
     */
    protected $cache;

    public function __construct(Container $app)
    {
        $this->cache = $app['cache'];
    }
    /**
     * Resolve a given value that match the key from the storage
     *
     * @param string $key
     * @return serialized
     *
     */
    public function get($key)
    {
        return $this->cache->get($key, null);
    }

    /**
     * Add new value to the storage
     *
     * @param string $key
     * @return void
     *
     */
    public function put($key, $value, $expiration = null)
    {
        $this->cache->put($key, $value, $expiration);
    }

    /**
     * Removes a value from the storage
     *
     * @param string $key
     * @return boolean
     *
     */
    public function delete($key): bool
    {
        return $this->cache->forget($key);
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
        return !is_null($this->cache->get($key, null)) ? true : false;
    }

    /**
     * Clear all values from the storage
     *
     * @return void
     */
    public function flush()
    {
        $this->cache->flush();
    }
}
