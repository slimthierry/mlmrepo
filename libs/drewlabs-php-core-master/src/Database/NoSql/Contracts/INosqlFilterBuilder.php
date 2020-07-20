<?php

namespace Drewlabs\Core\Database\NoSql\Contracts;

interface INosqlFilterBuilder
{

 /**
  * Group the query result by a given key
  *
  * @param string $key
  * @param string $collection
  * @return static
  */
    public function groupBy($key, $collection);

    /**
     * Sort the query result in the Ascending or Descending order
     *
     * @param string|array $keys
     * @param string|null $ord
     * @return static
     */
    public function sortBy($keys, $ord = null);

    /**
     * Takes a specific amount of items
     *
     * @param integer $limit
     * @return static
     */
    public function limit(int $limit);

    /**
     * Skip a specific number of items
     *
     * @param integer $items
     * @return static
     */
    public function skip(int $items);

    /**
     * Specify the list of columns to returns
     *
     * @param array $list
     * @return static
     */
    public function select(array $list);

    /**
     * Apply count filter to the db query filters
     *
     * @return static
     */
    public function count();

    /**
     * Return the list of builded filters
     *
     * @return mixed
     */
    public function getFilters();

    /**
     * Reinitialize the filters array
     *
     * @return void
     */
    public function unsetFilters();
}
