<?php

namespace Drewlabs\Core\Database\NoSql\Mongo\Concerns;

use Drewlabs\Core\Database\NoSql\Contracts\INosqlFilterBuilder;

class MongoFiltersBuilder implements INosqlFilterBuilder
{

    /**
     * Array of mongo db aggregation framework stages definition
     *
     * @var array
     */
    protected $filters = array();

    /**
     * Group the query result by a given key
     *
     * @param string $key
     * @param string $collection
     * @return static
     */
    public function groupBy($key, $collection)
    {
        $this->filters[] = array('$group' => array('_id' => '$' . $key, $collection => array('$push' => '$$ROOT')));
        return $this;
    }

    /**
     * Sort the query result in the Ascending or Descending order
     *
     * @param string|array $keys
     * @param string|null $ord
     * @return static
     */
    public function sortBy($keys, $ord = null)
    {
        if (\is_string($keys)) {
            $this->filters[] = array('$sort' => array($keys => strtolower($ord) === 'desc' ? -1 : 1));
        }
        if (\is_array($keys)) {
            $filters = array();
            foreach ($keys as $v) {
                # code...
                if (count($v) !== 2) {
                    throw new \InvalidArgumentException('List of sorting keys must be an array of 2 items[$key, $ord] arrays');
                }
                $filters[] = array($v[0] => strtolower($v[1]) === 'desc' ? -1 : 1);
            }
            $this->filters[] = array('$sort' => $filters);
        }
        return $this;
    }

    /**
     * Takes a specific amount of items
     *
     * @param integer $limit
     * @return static
     */
    public function limit(int $limit)
    {
        $this->filters[] = array('$limit' => $limit);
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
        $this->filters[] = array('$skip' => $items);
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
        if (!in_array('*', $list)) {
            $project = array();
            foreach ($list as $v) {
                # code...
                $project[$v] = 1;
            }
            $this->filters[] = array('$project' => $project);
        }
        return $this;
    }

    /**
     * Apply count operator to the previous aggregation stage
     *
     * @return static
     */
    public function count()
    {
        $this->filters[] = array('$count' => 'count');
        return $this;
    }

    /**
     * Return the list of builded filters
     *
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Reinitialize the filters array
     *
     * @return void
     */
    public function unsetFilters()
    {
        $this->query = array();
    }
}
