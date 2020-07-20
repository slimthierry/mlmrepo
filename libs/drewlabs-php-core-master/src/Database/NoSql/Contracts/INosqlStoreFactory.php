<?php

namespace Drewlabs\Core\Database\NoSql\Contracts;

interface INosqlStoreFactory
{
    /**
     * Make an istance of the NoSqlConnection
     *
     * @param mixed $client
     * @return INosqlStoreFactory
     */
    public function make($diver);

    /**
     * Return the created repository
     *
     * @return INosqlStore
     */
    public function resolve();
}
