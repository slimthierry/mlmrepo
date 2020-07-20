<?php

namespace Drewlabs\Contracts\Data;

interface IModelable
{
    /**
     * Insert new item to the data store
     *
     * @param array $item
     *
     * @return mixed
     */
    public function add(array $items);

    /**
     * Get or select an item from the data store based on some conditions
     *
     * @param mixed $conditions
     * @param bool $relations
     * @return mixed
     */
    public function findWith(array $conditions, bool $relations = false);
    /**
     * Fetch all data from the data storage with their related relationship
     *
     * @param bool $relations
     * @param array $columns
     * @return mixed
     */
    public function getAll(bool $relations = false, array $columns = array('*'));

    /**
     * Update an item in the data store
     *
     * @param mixed $conditions
     * @param array $update_values
     *
     * @return int
     */
    public function updateWith($conditions, array $update_values);

    /**
     * Remove an item from the data store
     *
     * @param array $conditions
     *
     * @return int
     */
    public function deleteWith(array $conditions = null);

    /**
     * Returns the primaryKey of the given model
     *
     * @return string
     */
    public function getPrimaryKey();

    /**
     * Returns the value of the primary of the model
     *
     * @return string
     */
    public function getKey();

    /**
     * Returns the collection associated with the given model
     *
     * @return string
     */
    public function getEntity();

    /**
     * Returns the collection unique identifying name
     *
     * @return string
     */
    public function getEntityUniqueName();

    /**
     * Convert list of model attributes | properties into array
     *
     * @return array
     */
    public function attributesToArray();

    /**
     * Convert a model and it relationships into an array
     *
     * @return array
     */
    public function toArray();
}
