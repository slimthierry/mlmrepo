<?php

namespace Drewlabs\Core\Database\NoSql\Contracts;

interface INosqlStore
{
    /**
     * Call to excute a command or query on the data store
     *
     * @param string $function
     * @param array $params
     * @return mixed
     */
    public function executeQuery($function, array $params);

    /**
     * Start a new DB trasaction for the current session
     *
     * @return void
     */
    public function startTransaction();

    /**
     * Commit a transaction operation when done
     *
     * @return void
     */
    public function completeTransaction();

    /**
     * Abort transaction operation
     *
     * @return boolean
     */
    public function cancel();

    /**
     * Return the injected connection provider
     *
     * @return INosqlConnection
     */
    public function getConnection();

    /**
     * Returns database client library
     *
     * @return mixed
     */
    public function getDBClient();

    /**
     * Select data store collection to work with
     *
     * @param string $collection
     * @param string $document_id
     * @return INosqlStore
     */
    public function selectCollection($collection, $document_id = null);
}
