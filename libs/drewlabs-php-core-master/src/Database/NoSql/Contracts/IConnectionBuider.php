<?php

namespace Drewlabs\Core\Database\NoSql\Contracts;

interface IConnectionBuider
{
    /**
     * Create a new Mongo store provider
     *
     * @param string $mgo_user
     * @param string $mgo_pass
     * @param string $mgo_host
     * @param string $mgo_port
     * @param string $mgo_db
     * @param string $auth_source
     * @param array|null $options
     * @param array|null $options
     * @return \INosqlConnection
     */
    public function create($mgo_user, $mgo_pass, $mgo_host, $mgo_port, $mgo_db, $auth_source, $options = array(), $driver_options = array());
}
