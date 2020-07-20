<?php

namespace Drewlabs\Core\Database\NoSql\Mongo;

use Drewlabs\Core\Database\NoSql\Contracts\INosqlConnection;
use Drewlabs\Core\Database\NoSql\Contracts\INosqlStore;
use MongoDB\Client;
use MongoDB\Driver\Session;

class MongoStore implements INosqlStore
{
    /**
     * Nosql Store connection implementation
     *
     * @var INosqlConnection
     */
    protected $conn;

    /**
     * The database to work with
     *
     * @var string
     */
    protected $database;

    /**
     * MongoDB session started by the client
     * @var Session
     */
    protected $session;

    public function __construct(INosqlConnection $conn, $db = 'test')
    {
        $this->database = $db;
        if (method_exists($conn, 'session')) {
            $this->session = $conn->{'session'}();
        }
    }

    /**
     * Start a new DB trasaction for the current session
     *
     * @return void
     */
    public function startTransaction()
    {
        is_null($this->session) ?: $this->session->startTransaction();
    }

    /**
     * Commit a transaction operation when done
     *
     * @return void
     */
    public function completeTransaction()
    {
        is_null($this->session) ?: $this->session->commitTransaction();
    }

    /**
     * Abort transaction operation
     *
     * @return boolean
     */
    public function cancel()
    {
        is_null($this->session) ?: $this->session->abortTransaction();
    }

    /**
     * Call to excute a command or query on the data store
     *
     * @param string $method
     * @param array $params
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function executeQuery($method, array $params)
    {
        if (\method_exists($this->conn, $method)) {
            return \call_user_func_array(array($this->conn, $method), $params);
        }
        throw new \RuntimeException('Invalid method call {$method} on class : ' . get_class($this->conn));
    }

    /**
     * Return the injected connection provider
     *
     * @return INosqlConnection
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Set the collection to work on in the current transaction
     *
     * @param string collection
     * @param string $document_id
     * @return static
     */
    public function selectCollection($collection, $document_id = null)
    {
        if (\is_string($collection)) {
            $this->conn = $this->conn->setCollection($collection, $document_id);
            return $this;
        }
        throw new \InvalidArgumentException("Collection name must be of type string");
    }

    /**
     * Returns database client library
     *
     * @return Client
     */
    public function getDBClient()
    {
        return $this->conn->getClient();
    }

    /**
     * Select collection in the mongo database
     *
     * @param string|null $collection
     * @return void
     */
    public static function collection(? string $collection)
    {
        $store = MongoStoreBuilber::create(getenv('MONGODB_USERNAME', 'testuser'), getenv('MONGODB_PASSWORD', "testpass"), getenv('MONGODB_HOST', "192.168.103.100"), getenv('MONGODB_PORT', "27000"), getenv('MONGODB_DATABASE', "test"), getenv('MONGODB_AUTHSOURCE', "admin"));
        return $store = $store->selectCollection($collection);
    }

    /**
     * Class instances destructor
     */
    public function __destruct()
    {
        unset($this->conn);
    }
}
