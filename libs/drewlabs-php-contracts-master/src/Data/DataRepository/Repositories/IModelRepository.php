<?php

namespace Drewlabs\Contracts\Data\DataRepository\Repositories;

interface IModelRepository
{

    /**
     * Get a collection of model from the data storage
     *
     * @return mixed
     */
    public function all($columns = array('*'));

    /**
     * Run model paginate query
     *
     * @param integer $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = array('*'));

    /**
     * Add or insert new record to the data storage
     * @param array $data
     * @param bool $parse_inputs
     * @param bool $upsert
     * @param array $upsertConditions
     * @return mixed
     */
    public function insert(array $values, bool $parse_inputs = false, $upsert = false, $upsertConditions = array());

    /**
    * Create many model entries in the data storage
    *
    * @param array $values
    * @param boolean $parse_inputs
    * @return mixed
    */
    public function insertMany(array $values, $parse_inputs = true);

    /**
     * Return a new model matching a certain condition
     *
     * @param array $conditions
     * @param array $columns
     * @return array|mixed
     */
    public function find(array $conditions = array(), array $columns = array('*'));

    /**
     * Return a new model with the given id
     *
     * @param int $id
     * @param array $columns
     * @return mixed
     */
    public function findById($id, array $columns = array('*'));

    /**
     * Update a given record in the data storage using model id
     *
     * @param int $id
     * @param array $data
     * @param bool $parse_inputs
     * @return int
     */
    public function updateById($id, array $data, bool $parse_inputs = true);

    /**
     * Update a given record in the data storage based on condition
     *
     * @param array $data
     * @param array $conditions
     * @param bool $parse_inputs
     * @return int
     */
    public function update(array $data, array $conditions = array(), bool $parse_inputs = true, bool $mass_update =  false);

    /**
     * Delete record from data storage based on model id
     *
     * @param int|mixed $id
     * @return int
     */
    public function deleteById($id);

    /**
     * Delete record from data storage based on condition
     *
     * @param array $conditions
     * @param bool $mass_delete
     * @return int
     */
    public function delete(array $conditions = array(), bool $mass_delete =  false);

    /**
     * Get the current repository model class name
     *
     * @return string
     */
    public function getModel();
}
