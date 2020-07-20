<?php

namespace Drewlabs\Core\Data\Traits;

/**
 * Model trait provider extensions for basic operations offered by Eloquent ORM Model class
 */
trait Model
{
    /**
     * Insert new item to the data store
     *
     * @param array $item
     *
     * @return mixed
     */
    public function add(array $items)
    {
        return $this->create($items);
    }

    /**
     * Get or select an item from the data store based on some conditions
     *
     * @param mixed $conditions
     * @param bool $relations
     * @return mixed
     */
    public function findWith(array $conditions, bool $relations)
    {
        return $this->where($conditions);
    }

    /**
     * Get or select an item from the data store having a date matching some given value
     *
     * @param string $date
     * @param bool $relations
     * @return mixed
     */
    public function havingDate(string $date)
    {
        //  return $this->whereDate();
    }

    /**
     * Update an item in the data store
     *
     * @param mixed $conditions
     * @param array $update_values
     *
     * @return bool
     */
    public function updateWith($conditions, array $update_values): bool
    {
        return $this->update($conditions, $update_values);
    }

    /**
     * Remove an item from the data store
     *
     * @param array $conditions
     *
     * @return bool
     */
    public function deleteWith(array $conditions): bool
    {
        return $this->delete($conditions);
    }
}
