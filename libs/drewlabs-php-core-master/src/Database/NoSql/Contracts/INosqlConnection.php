<?php

namespace Drewlabs\Core\Database\NoSql\Contracts;

use IDatabaseTypes;

interface INosqlConnection
{
    /**
     * Insert item(s) to the store
     *
     * @param mixed $values
     * @return mixed
     */
    public function insert(array $values);

    /**
     * Insert many items to the data store
     *
     * @param array $values
     * @return array[int]
     */
    public function insertMany($values);

    /**
     * Update store value(s) based on a certain condtions
     *
     * @param array $conditions
     * @param array $values
     * @return int
     */
    public function update(array $conditions, array $values);

    /**
     * Delete item(s) from the store
     *
     * @param array $conditions
     * @return int
     */
    public function delete(array $conditions);

    /**
     * Get item(s) from the store based on a certain criteria
     *
     * @param array $conditions
     * @return \Traversable
     */
    public function find(array $conditions = array(), array $query_criteria = null);

    /**
     * Set the collection for the current transaction
     *
     * @param string $collection
     * @param string $document_id
     * @return INosqlConnection
     */
    public function setCollection($collection, $document_id);

    /**
     * Return the selected database collection
     *
     * @return string
     */
    public function getCollection();

    /**
     * Return the primaryKey of the selected database collection
     *
     * @return string
     */
    public function getCollectionKey();
    /**
     * Get the database server supported type provider
     *
     * @return IDatabaseTypes
     */
    public function getDatabaseTypes();
}
