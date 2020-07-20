<?php

namespace Drewlabs\Core\Database\NoSql;

use Drewlabs\Core\Database\NoSql\Contracts\INosqlConnection;

class DatabaseManager
{

    /**
     * Data storage connection driver
     *
     * @var INosqlConnection
     */
    protected $connection;

    /**
     * DbManager instance initializer
     *
     * @param string|null $connection_driver
     */
    public function __construct(? string $connection_driver = 'DEFAULT_DRIVER')
    {
        $this->connection = NosqlConnectionFactory::make($connection_driver);
    }

    /**
     * Select a collection in the database
     *
     * @param string|null $collection
     * @return static
     */
    public function collection(? string $collection)
    {
        $this->setConnection($this->connection->setCollection($collection, '_id'));
        return $this;
    }

    /**
     * Set the DbManager connection provider
     *
     * @param INosqlConnection $conn
     * @return void
     */
    private function setConnection(INosqlConnection $conn)
    {
        $this->connection = $conn;
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return BuilderFactory::makeBuilder($this->connection)->$method(...$parameters);
    }
}
