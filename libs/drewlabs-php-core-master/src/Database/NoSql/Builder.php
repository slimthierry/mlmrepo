<?php

namespace Drewlabs\Core\Database\NoSql;

use Drewlabs\Contracts\Data\IModelable;
use Drewlabs\Core\Database\NoSql\Contracts\IGrammar;
use Drewlabs\Core\Database\NoSql\Contracts\INosqlConnection;
use Drewlabs\Core\Database\NoSql\Contracts\INosqlFilterBuilder;
use Drewlabs\Utils\Arr;
use NosqlModel;

class Builder
{
    /**
     * Data storage connection driver
     *
     * @var INosqlConnection
     */
    protected $connection;

    /**
     * @var IGrammar
     */
    protected $grammar;

    /**
     * Database query filters builder
     *
     * @var INosqlFilterBuilder
     */
    protected $filter_builder;

    /**
     * @var NosqlModel
     */
    protected $model;

    public function __construct(INosqlConnection $conn)
    {
        $this->connection = $conn;
    }

    /**
     * Insert new item to the data store
     *
     * @param array $item
     * @param bool $many
     *
     * @return array[static]|static
     */
    public function insert(array $items)
    {
        // Checks if items is a list of array
        $isArrayList = \array_filter($items, 'is_array') === $items;
        if (!$isArrayList) {
            $items = array($items);
        }
        // Fills each items with the timestamps keys with values
        if ($this->model && ($this->model->time_stamps === true)) {
            $items = Arr::map($items, function ($it) {
                return array_merge($it, $this->connection->getDatabaseTypes()->getTimeStamps());
            });
        }
        $result = $this->connection->insertMany($items);
        $grammar = $this->grammar;
        $ids = Arr::map($result, function ($v) use ($grammar) {
            return $grammar->toDbPrimaryKeyType($v);
        });
        $queryResult = $this->runFindQuery($this->whereIn($this->connection->getCollectionKey(), (array)$ids)->grammar->getQuery());
        return $isArrayList ? $queryResult : $queryResult[0];
    }

    /**
     * Prepare a where query statement with the provided condition
     *
     * @param mixed $params
     * @param string $operator
     * @param mixed $value
     * @return static
     */
    public function where($params, $operator = null, $value = null)
    {
        // Get function arguments as an array of arguments
        $args = array_filter(\func_get_args(), function ($v) {
            return isset($v);
        });
        if (count($args) === 1) {
            if (!is_array($params)) {
                throw new \InvalidArgumentException('Parameter 1 passed to ' . __METHOD__ . ' must be an array of array if [$operator] and [$value] not provided');
            }
            $params = Arr::map($params, function ($v) {
                return $this->parseQueryParams($v);
            });
            $this->grammar = $this->grammar->prepareQuery($params);
        }
        if (count($args) === 2) {
            $args = $this->parseQueryParams($args);
            $this->grammar = $this->grammar->prepareQuery(array(array($args[0], "=", $args[1])));
        }

        if (count($args) === 3) {
            $args = $this->parseQueryParams($args);
            $this->grammar = $this->grammar->prepareQuery(array(array($args[0], $args[1], $args[2])));
        }
        return $this;
    }

    /**
     * Chain builded query statement with an OR clause
     *
     * @param mixed $params
     * @param string $operator
     * @param mixed $value
     * @return static
     */
    public function orWhere($params, $operator = null, $value = null)
    {
        $query = $this->grammar->getQuery();
        if (!is_null($query)) {
            $orQuery = $this->where($params, $operator, $value)->grammar->getQuery();
            $this->grammar = $this->grammar->combine($this->grammar->getOrOperator(), $query, $orQuery);
        }
        return $this;
    }

    /**
     * Build a where query that matches any of the values specified in an $values array.
     *
     * @param array $conditions
     * @return static
     */
    public function whereIn($field, array $values)
    {
        if (!\is_string($field) || !\is_array($values)) {
            throw new \InvalidArgumentException('[param 1] must be a valid PHP string  and [param 2] must be a valid PHP array');
        }
        if ($field === $this->connection->getCollectionKey()) {
            $grammar = $this->grammar;
            $values = Arr::map($values, function ($v) use ($grammar) {
                return $grammar->toDbPrimaryKeyType($v);
            });
        }
        $this->grammar = $this->grammar->prepareQuery(array(array($field, $this->grammar->getInOperator(), $values)));
        return $this;
    }

    /**
     * Call a delete method on the current model to remove an item in the data store
     *
     * @param array|null $conditions
     * @return int|bool
     */
    public function delete(array $conditions = null)
    {
        if (!is_null($conditions)) {
            $query = $this->where($conditions)->grammar->getQuery();
        } elseif (is_null($conditions) && isset($this->grammar)) {
            if ($this->model && isset($this->model {
                // Case deleting a model that was previously returned from the database
                $this->model->getPrimaryKey()})) {
                $query = $this->where($this->model->getPrimaryKey(), $this->model {
                    $this->model->getPrimaryKey()})->grammar->getQuery();
            } else {
                $query = $this->grammar->getQuery();
            }
            if (!isset($query)) {
                throw new \BadMethodCallException(__METHOD__ . ' not propperly called');
            }
        } else {
            throw new \BadMethodCallException(__METHOD__ . ' must have [$conditions] parameter set when not being called after a where query');
        }
        return $this->connection->delete($query);
    }

    /**
     * Call a update method on the current model to remove an item in the data store
     *
     * @param array $conditions
     * @param array $values
     * @return int|bool
     */
    public function update(array $values)
    {
        if (isset($this->grammar)) {
            if ($this->model && isset($this->model {
                // Case updating a model that was previously returned from the database
                $this->model->getPrimaryKey()})) {
                $query = $this->where($this->model->getPrimaryKey(), $this->model {
                    $this->model->getPrimaryKey()})->grammar->getQuery();
            } else {
                $query = $this->grammar->getQuery();
            }
            if (!isset($query)) {
                throw new \BadMethodCallException(__METHOD__ . ' mot propperly called');
            }
        } else {
            throw new \BadMethodCallException(__METHOD__ . ' must have [$conditions] parameter set when not being called after a where query');
        }
        return $this->connection->update($query, $values);
    }

    /**
     * Execute query generated by the query builder
     *
     * @param array $columns
     * @return array[mixed]
     */
    public function get(array $columns = array('*'))
    {
        if (!empty($columns)) {
            $this->filter_builder = $this->filter_builder->select($columns);
        }
        $query = $this->grammar->getQuery() ? $this->grammar->getQuery() : (is_array($this->filter_builder->getFilters()) ? array() : null);
        if (isset($query) && is_array($query)) {
            return $this->runFindQuery($query);
        }
        throw new \BadFunctionCallException("Call to " . __METHOD__ . " required query to be generated.");
    }

    /**
     * Execute query generated by the query builder
     *
     * @param int $pagination
     * @return array|mixed
     *
     * @throws \RuntimeException
     */
    public function paginate($pagination = 50, array $columns = array('*'))
    {
        throw new \RuntimeException('Unimplemented method ' . __METHOD__ . ' on class ' . __CLASS__ . '');
    }

    /**
     * Execute query generated by the query builder
     *
     * @param array $query
     * @return array[mixed]
     */
    private function runFindQuery(array $query)
    {
        $values = $this->connection->find($query, $this->filter_builder->getFilters());
        if ($this->model) {
            $result = $this->morphModel($values);
        }
        $this->grammar->unsetQuery();
        $this->filter_builder->unsetFilters();
        return $result;
    }

    /**
     * Group the query result by a given key
     *
     * @param string $key
     * @return static
     */
    public function group($key)
    {
        $this->filter_builder = $this->filter_builder->groupBy($key, $this->connection->getCollection());
        return $this;
    }

    /**
     * Sort the query result in the Ascending or Descending order
     *
     * @param string|array $keys
     * @param string|null $ord
     * @return static
     */
    public function orderBy($keys, $ord = null)
    {
        $this->filter_builder = $this->filter_builder->sortBy($keys, $ord);
        return $this;
    }

    /**
     * Takes a specific amount of items
     *
     * @param integer $limit
     * @return static
     */
    public function take(int $limit)
    {
        $this->filter_builder = $this->filter_builder->limit($limit);
        return $this;
    }

    /**
     * Skip a specific number of items
     *
     * @param integer $items
     * @return static
     */
    public function skip(int $items)
    {
        $this->filter_builder = $this->filter_builder->skip($items);
        return $this;
    }

    /**
     * Specify the list of columns to returns
     *
     * @param array $list
     * @return static
     */
    public function select(array $list)
    {
        $this->filter_builder = $this->filter_builder->select($list);
        return $this;
    }

    /**
     * Retrieve the "count" result of the query.
     *
     * @param  string  $columns
     * @return int
     */
    public function count($columns = '*')
    {
        if (!($columns === '*')) {
            $this->filter_builder = $this->filter_builder->select(explode(',', $columns));
        }
        $query = $this->grammar->getQuery() ? $this->grammar->getQuery() : (is_array($this->filter_builder->getFilters()) ? array() : null);
        return $result = count($this->connection->find($query, $this->filter_builder->getFilters())->{"toArray"}());
    }

    /**
     * Set the Grammar instance object
     *
     * @param IGrammar $grammar
     * @return static
     */
    public function setQueryGrammar(IGrammar $grammar)
    {
        $this->grammar = $grammar;
        return $this;
    }

    /**
     * Set the model binding for the current builder
     *
     * @param IModelable $model
     * @return static
     */
    public function setModel(IModelable $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Set the INosqlFilterBuilder instance object
     *
     * @param INosqlFilterBuilder $grammar
     * @return static
     */
    public function setFiltersBuilder(INosqlFilterBuilder $filter)
    {
        $this->filter_builder = $filter;
        return $this;
    }

    /**
     * Parse query parameters and convert to db specific type is required
     *
     * @param array $params
     * @return array
     */
    private function parseQueryParams(array $params)
    {
        if (count($params) === 2) {
            if ($params[0] === $this->connection->getCollectionKey()) {
                $params[1] = $this->grammar->toDbPrimaryKeyType($params[1]);
            }
        }
        if (count($params) === 3) {
            if ($params[0] === $this->connection->getCollectionKey()) {
                $params[2] = $this->grammar->toDbPrimaryKeyType($params[2]);
            }
        }
        return $params;
    }

    private function morphModel(\Traversable $values)
    {
        $list = [];
        foreach ($values as $value) {
            $self = clone $this->model;
            $list[] = $self->setAttributes((array)$value);
        }
        return $list;
    }
}
