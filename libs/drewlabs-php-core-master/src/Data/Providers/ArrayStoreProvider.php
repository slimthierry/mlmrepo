<?php

namespace Drewlabs\Core\Data\Providers;

use Drewlabs\Contracts\Data\Store;
use Drewlabs\Utils\Arr;

class ArrayStoreProvider implements Store
{
    /**
     * Singleton instance of the class
     *
     * @var static
     */
    protected static $instance;

    /**
     * Data storage instance using php array
     *
     * @var array
     */
    protected $store;

    /**
     * Represent a collection name
     *
     * @var string
     */
    protected $collection;

    /**
     * Unique key for each element of a given collection
     *
     * @var string
     */
    protected $collection_key;

    /**
     * Used for building consecutive queries
     *
     * @var array
     */
    protected $builder;

    private function __construct()
    {
    }
    /**
     * Prevent from being unserialized (which would create a second instance of it)
     */
    private function __wakeup()
    {
    }

    /**
     * Prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }
    /**
     * Make a new instance if not already created and return existing instance if exists
     *
     * @return static
     */
    public static function getInstance(): ArrayStoreProvider
    {
        if (null === static::$instance) {
            static::$instance = new static();
            static::$instance->store = array();
        }

        return static::$instance;
    }

    /**
     * Access a collection to query
     *
     * @param string $collection
     * @param string $primary_key (Unique identifier for each column)
     *
     * @return static
     */
    public static function setCollection($collection, $primary_key = 'id')
    {
        $self = static::getInstance();
        $self->collection = $self->set($collection);
        $self->collection_key = $primary_key;
        return $self;
    }

    /**
     * Set a collection in the store based on item $key
     *
     * @param string $collection
     *
     * @return string
     */
    public function set($collection)
    {
        if (!array_key_exists($collection, $this->store)) {
            $this->store[$collection] = array();
        }
        return $collection;
    }

    /**
     * Get all of the query result set
     *
     * @return array
     */
    public function get()
    {
        return isset($this->builder) && !empty($this->builder) ? $this->builder : [];
    }

    /**
     * Get the first match of a query result set
     *
     * @return mixed
     */
    public function first()
    {
        return isset($this->builder) && !empty($this->builder) ? (object) $this->builder[0] : null;
    }

    /**
     * Insert new item to the data store
     *
     * @param array $item
     *
     * @return static
     */
    public function insert(array $item)
    {
        $this->store[$this->collection][] = $item;
        return $this;
    }

    /**
     * Insert many items to the data store
     *
     * @param array $item
     *
     * @return static
     */
    public function insertMany(array $items)
    {
        array_push($this->store[$this->collection], ...$items);
        return $this;
    }

    /**
     * Get or select items from the data store based on conditions
     *
     * @param mixed $conditions
     * @param string $operator
     * @param string $value
     *
     * @return mixed
     */
    public function findBy($conditions = array(), $operator = null, $value = null)
    {
        // Call the where method passing matching conditions
        return $this->where($conditions, $operator, $value)->get();
    }

    /**
     * Get or select an item from the data store based on item identifier
     *
     * @param mixed $conditions
     *
     * @return mixed
     */
    public function findById($id)
    {
        // Call the where method passing conditions matching id value
        return $this->where($this->collection_key, $id)->first();
    }

    /**
     * Update an item in the data store by it id
     *
     * @param mixed $id
     *
     * @return bool
     */
    public function updateById($id, $update_values): bool
    {
        // Call the update method passing conditions matching id value
        return $this->updateBy(array(array($this->collection_key, $id)), $update_values);
    }

    /**
     * Update an item in the data store
     *
     * @param mixed $conditions
     * @param array $update_values
     *
     * @return bool
     */
    public function updateBy($conditions, array $update_values): bool
    {
        // Find elements matching the given conditions
        $matching_elements = $this->execute($conditions);
        if (count($matching_elements) === 0) {
            return false;
        }
        foreach ($matching_elements as $key => $value) {
            // find indexes of elements to update
            $index = Arr::find_index_by($this->store[$this->collection], $this->collection_key, is_object($value) ? $value->{$this->collection_key} : $value[$this->collection_key]);
            if ($index !== -1) {
                // Update current element with update values
                foreach ($update_values as $k => $v) {
                    # code...
                    is_array($this->store[$this->collection][$index]) ? $this->store[$this->collection][$index][$k] = $v : $this->store[$this->collection][$index]->{$k} = $v;
                }
            }
        }
        return true;
    }

    /**
     * Delete an item from the the data store by it id
     *
     * @param mixed $id
     *
     * @return bool
     */
    public function deleteById($id): bool
    {
        // Call the delete method passing conditions matching id value
        return $this->deleteBy($this->collection_key, $id);
    }

    /**
     * Remove an item from the data store
     *
     * @param mixed $conditions
     * @param string $operator
     * @param string $value
     *
     * @return bool
     */
    public function deleteBy($conditions = null, $operator = null, $value = null): bool
    {
        // Find elements matching the given conditions
        $matching_elements = $this->query($conditions, $operator, $value);
        if (count($matching_elements) === 0) {
            return false;
        }
        // Remove matching elements from the collection
        foreach ($matching_elements as $key => $value) {
            $index = Arr::find_index_by($this->store[$this->collection], $this->collection_key, is_object($value) ? $value->{$this->collection_key} : $value[$this->collection_key]);
            if ($index !== -1) {
                unset($this->store[$this->collection][$index]);
            }
        }
        return true;
    }

    /**
     * Find entity that match conditions
     *
     * @param mixed $conditions
     * @param string $operator
     * @param string $value
     *
     * @throws \RuntimeException
     *
     * @return static
     */
    public function where($conditions = null, $operator = null, $value = null)
    {
        $this->builder = $this->query($conditions, $operator, $value);
        return $this;
    }

    /**
     * Find entities that matches one condition or the other
     *
     * @param mixed $conditions
     * @param string $operator
     * @param string $value
     *
     * @throws \RuntimeException
     *
     * @return static
     */
    public function orWhere($conditions = null, $operator = null, $value = null)
    {
        if (!isset($this->builder)) {
            return $this->where($conditions, $operator, $value);
        }
        $this->builder = array_intersect($this->builder, $this->query($conditions, $operator, $value));
        return $this;
    }

    /**
     * Query for data based on conditions
     *
     * @param array $conditions
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    public function execute(array $conditions = null)
    {
        // Clone in other to not get reference to the object
        $temp_results = isset($this->store[$this->collection]) ? $this->store[$this->collection] : [];
        if (!isset($conditions)) {
            return $temp_results;
        }
        if (!is_array($conditions) || empty($conditions)) {
            // throw new \RuntimeException('conditions array must not be empty');
            return $temp_results;
        }
        foreach ($conditions as $item) {
            if (is_array($item)) {
                $temp_results = Arr::where($temp_results, function ($v) use ($item) {
                    if (count($item) == 2) {
                        return $this->itemMatches($v, $item[0], $item[1]);
                    }
                    if (count($item) == 3) {
                        return $this->itemMatches($v, $item[0], $item[2], $item[1]);
                    }
                    throw new \RuntimeException('invalid conditions parameters count. Expected 2 or 3 elements, got ' . count($item) . ' elements');
                    // return false;
                });
            } else {
                throw new \RuntimeException('invalid conditions parameters, array is required');
                // return $temp_results;
            }
        }
        return iterator_to_array($this->toObject($temp_results));
    }
    /**
     * Return a filtering condition based on parameters
     *
     * @param mixed $item
     * @param string $needle
     * @param string $search
     *
     * @throws \RuntimeException
     *
     * @return bool
     */
    private function itemMatches($item, $needle, $search, $operator = "=")
    {
        switch (strtolower($operator)) {
   case "=":
    $lhs = is_array($item) ? $item[$needle] : $item->{$needle};
    return $lhs === $search;
   case "<>":
    $lhs = is_array($item) ? $item[$needle] : $item->{$needle};
    return $lhs !== $search;
   case ">":
    $lhs = is_array($item) ? $item[$needle] : $item->{$needle};
    return $lhs > $search;
   case "<":
    $lhs = is_array($item) ? $item[$needle] : $item->{$needle};
    return $lhs < $search;
   case "like":
    // TODO : perform a regular expression
    return;
   default:
    throw new \RuntimeException('invalid comparison operator ' . $operator);
  }
    }

    private function query($search = null, $operator = null, $value = null)
    {
        // Get function arguments as an array of arguments
        $args = array_filter(\func_get_args(), function ($v) {
            return isset($v);
        });
        $args_num = count($args);
        if ($args_num === 1) {
            return $this->execute($args[0]);
        }
        if ($args_num === 2) {
            return $this->execute(array(array($args[0], $args[1])));
        }
        if ($args_num === 3) {
            return $this->execute(array(array($args[0], $args[1], $args[2])));
        }
    }

    /**
     * Generator to convert an array of array to an array of object
     */
    private function toObject(array $items)
    {
        if (!is_array($items)) {
            return $items;
        }
        foreach ($items as $key) {
            # code...
            yield is_array($key) ? (object) $key : $key;
        }
    }
}
