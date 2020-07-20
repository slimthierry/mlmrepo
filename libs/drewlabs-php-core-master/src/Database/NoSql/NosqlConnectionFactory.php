<?php

namespace Drewlabs\Core\Database\NoSql;

use Drewlabs\Core\Database\NoSql\Contracts\INosqlConnection;

class NosqlConnectionFactory
{

    /**
     * Make an istance of the NoSqlConnection
     *
     * @param string $driver
     * @return INosqlConnection
     */
    public static function make($driver = 'mongo')
    {
        $driver = $driver ? $driver : getenv('DEFAULT_NOSQL_DRIVER', 'mongo');
        return static::buildConnectionProvider(strtolower($driver));
    }

    /**
     * Create an instance of MonogoDB Connection Provider
     *
     * @return void
     */
    public static function buildConnectionProvider(? string $driver)
    {
        $class = '\\Domain\\Providers\\Database\\NoSql\\' . ucfirst($driver) . '\\' . ucfirst($driver) . 'ConnectionBuilder';
        return (new $class)->create(getenv('NOSQLDB_USERNAME', 'testuser'), getenv('NOSQLDB_PASSWORD', "testpass"), getenv('NOSQLDB_HOST', "127.0.0.1"), getenv('NOSQLDB_PORT', "27000"), getenv('NOSQLDB_DATABASE', "test"), getenv('NOSQLDB_AUTHSOURCE', "admin"));
    }
}
