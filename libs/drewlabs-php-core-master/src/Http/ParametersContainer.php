<?php

namespace Drewlabs\Core\Http;

use Drewlabs\Contracts\Container;

class ParametersContainer implements Container
{
    /**
     * @var array
     */
    protected $parameters;

    public function __construct(array $items = array())
    {
        $this->parameters = $items;
    }

    /**
     * Static instance creator
     *
     * @param static $container
     *
     * @return static
     */
    public static function from(ParametersContainer $container)
    {
        $self = new static;
        $self->parameters = $container->parameters;
        return $self;
    }
    /**
     * Get an item from the container based on item $key
     *
     * @param string $item
     *
     * @return mixed
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }
    }

    /**
     * Check if container has a given value
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key) : bool
    {
        return array_key_exists($key, $this->parameters);
    }

    /**
     * Get all container values
     *
     * @return array|mixed
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     * Set a value to a container key
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function set($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Self property accessor
     *
     * @param string $name
     *
     * @return void
     */
    public function __get($name)
    {
        if ($name === 'parameters') {
            return $this->{$name};
        }
        return new \RuntimeException("Invalid property access");
    }
}
