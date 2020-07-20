<?php

namespace Drewlabs\Contracts\Data;

interface Store
{
    /**
     * Insert new item to the data store
     *
     * @param array $item
     *
     * @return mixed
     */
    public function insert(array $item);

    /**
     * Get or select an item from the data store
     *
     * @param mixed $conditions
     *
     * @return mixed
     */
    public function findBy(array $conditions);

    /**
     * Update an item in the data store
     *
     * @param mixed $conditions
     * @param array $update_values
     *
     * @return bool
     */
    public function updateBy($conditions, array $update_values): bool;

    /**
     * Remove an item from the data store
     *
     * @param mixed $conditions
     *
     * @return bool
     */
    public function deleteBy($conditions): bool;
}
