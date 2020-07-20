<?php

namespace Drewlabs\Core\Database\NoSql;

use Drewlabs\Contracts\Data\IModelable;
use Drewlabs\Core\Database\NoSql\Builder;
use Drewlabs\Core\Database\NoSql\Contracts\INosqlConnection;
use Drewlabs\Core\Database\NoSql\NosqlConnectionFactory;
use Drewlabs\Contracts\Data\IRelatable;

abstract class NosqlModel implements IModelable, \JsonSerializable, \ArrayAccess, IRelatable
{

    /**
     * Collection name
     *
     * @var string $entity
     */
    protected $entity;

    /**
     * The name of the collection primary key or UUID
     *
     * @var string
     */
    protected $primaryKey;

    /**
     * Returns a unique name of an entity in request input mapping
     *
     * @var string
     */
    protected $entity_uniq_name;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * Data storage connection driver
     *
     * @var INosqlConnection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $driver;

    /**
     * Tells the model to add time_stamps to the data entries or not
     *
     * @var boolean
     */
    public $time_stamps = true;

    /**
     * List of properties to not include in model output
     *
     * @var array
     */
    protected $guards = [];

    /**
     * Create a new NosqlModel instance
     *
     */
    public function __construct()
    {
        $this->connection = $this->boot();
    }

    /**
     * Initiate model and set selected collection
     *
     * @return INosqlConnection
     */
    protected function boot()
    {
        return NosqlConnectionFactory::make($this->driver)->setCollection(
            $this->entity,
            $this->getPrimaryKey()
        );
    }

    /**
     * Set the query builder instance on the current model
     *
     * @return Builder
     */
    public function newQuery()
    {
        return BuilderFactory::makeBuilder($this->connection)->setModel($this);
    }

    /**
     * @inheritDoc
     */
    public function add(array $items)
    {
        return $this->newQuery()->insert($items);
    }

    /**
     * @inheritDoc
     */
    public function findWith(array $conditions, bool $relations = false)
    {
        return $this->newQuery()->where($conditions);
    }
    /**
     * @inheritDoc
     */
    public function getAll(bool $relations = false, array $columns = array('*'))
    {
        return $this->newQuery()->get($columns);
    }

    /**
     * @inheritDoc
     */
    public function havingDate($field, $date, bool $relations = false)
    {
        return $this->newQuery()->where(array($field, "=:$date,date"));
    }

    /**
     * Get or select an item from the data store having a date matching some given value
     *
     * @param string $field
     * @param string $min_date
     * @param string $max_date
     * @param bool $relations
     * @return mixed
     */
    public function havingDateBetween($field, $min_date, $max_date, bool $relations)
    {
        return $this->newQuery()->where(array($field, ">=:$min_date,date|<=:$max_date,date"));
    }

    /**
     * @inheritDoc
     */
    public function updateWith($conditions, array $values)
    {
        return $this->newQuery()->where($conditions)->update($values);
    }

    /**
     * @inheritDoc
     */
    public function deleteWith(array $conditions = null)
    {
        return $this->newQuery()->where($conditions)->delete($conditions);
    }

    /**
     * Set the current model attributes
     *
     * @param array $attributes
     * @return static
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
    /**
     * @inheritDoc
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @inheritDoc
     */
    public function getRelations()
    {
        return $this->{'relations'} ? $this->{'relations'} : [];
    }

    /**
     * @inheritDoc
     */
    public function getEntityUniqueName()
    {
        return $this->entity_uniq_name;
    }

    /**
     * Class instance properties getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->attributesToArray();
    }

    /**
     * Checks if key exists on the model instance
     *
     * @param [type] $offset
     * @return void
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes) && isset($this->attributes[$offset]);
    }
    /**
     * Access model property using array index based of assoc key
     *
     * @param int|string $offset
     * @return void
     */
    public function offsetGet($offset)
    {
        return isset($this->attributes) && isset($this->attributes[$offset]) ? $this->attributes[$offset] : null;
    }
    /**
     * Set a property value on the model attributes array
     *
     * @param int|string $offset
     * @param mixed $value
     * @return void
     * @throws \RuntimeException
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException("$offset property must be set internally");
    }
    /**
     * Unset a given member of the current instance
     *
     * @param int|string $offset
     * @return void
     * @throws \RuntimeException
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException("Unable to unset $offset, Must be set internally");
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->newQuery()->$method(...$parameters);
    }

    /**
     * Executed when called clone on the current object
     *
     * @return static
     */
    public function __clone()
    {
        return new static;
    }

    private function attributesToArray()
    {
        if (is_null($this->attributes)) {
            return null;
        }
        $tags = array();
        foreach ($this->attributes as $k => $tag) {
            # code...
            if (in_array($k, $this->guards)) {
                continue;
            }
            $tags[$k] = $this->attributes[$k];
        }
        return $tags;
    }
}
