<?php

namespace Drewlabs\Core\Database\NoSql\Mongo;

use Drewlabs\Core\Database\NoSql\Contracts\IDatabaseTypes;
use Drewlabs\Core\Database\NoSql\Contracts\INosqlConnection;
use Drewlabs\Core\Database\NoSql\Mongo\Exceptions\ReadException;
use Drewlabs\Core\Database\NoSql\Mongo\Exceptions\WriteException;
use MongoDB\Client as MongoClient;
use Drewlabs\Utils\Arr;
use MongoDB\BSON\ObjectId;


class MongoConnectionProvider implements INosqlConnection
{

    /**
     * MongoDB Client implementation
     *
     * @var MongoClient
     */
    protected $client;

    /**
     * The database to work with
     *
     * @var string
     */
    protected $database;

    /**
     * Current collection name to apply transaction to
     *
     * @var string
     */
    protected $collection;

    /**
     * Unique id field of the selected collection
     *
     * @var string
     */
    protected $document_id;

    /**
     * Creat a new instance of the mongodb connection using the MongoClient library
     *
     * @param MongoClient $client
     */
    public function __construct(MongoClient $client)
    {
        $this->client = $client;
    }

    /**
     * Start a new Client session
     *
     * @return void
     */
    public function session()
    {
        try {
            return $this->client->startSession();
        } catch (\MongoDB\Driver\Exception\RuntimeException $ex) {
            return null;
        }
    }

    /**
     * Insert item(s) to the store
     *
     * @param array $values
     * @return string|mixed
     */
    public function insert(array $values)
    {
        $query_result = $this->client->selectCollection($this->database, $this->collection)->insertOne($values);
        return $query_result->getInsertedId();
    }

    /**
     * Insert many items to the data store
     *
     * @param array $values
     * @return array[int]
     */
    public function insertMany($values)
    {
        $query_result = $this->client->selectCollection($this->database, $this->collection)->insertMany($values);
        return $query_result->getInsertedIds();
    }

    /**
     * Update store value(s) based on a certain condtions
     *
     * @param array $conditions
     * @param mixed $values
     * @return int
     */
    public function update(array $conditions, array $values)
    {
        try {
            $query_result = $this->client
                ->selectCollection($this->database, $this->collection)
                ->updateMany($conditions, array('$set' => $values, '$currentDate' => array('lastModified' => true)));
            return $query_result->getModifiedCount();
        } catch (\MongoDB\Driver\Exception\BulkWriteException $ex) {
            throw new WriteException($ex->getMessage());
        }
    }

    /**
     * Update or insert documents in the data store
     *
     * @param array $conditions
     * @param array $values
     * @return mixed
     */
    public function updateOrCreate(array $conditions, array $values)
    {
        $query_result = $this->client->selectCollection($this->database, $this->collection)
            ->update($conditions, array('$set' => $values), array('upsert' => true));
        if ($query_result->getUpsertedCount() > 0) {
            // returns the result count o the inserted documents
            return $query_result->getUpsertedIds();
        } else {
            return $query_result->getModifiedCount() > 0 ? true : false;
        }
    }

    /**
     * Update document in the store with a specific id
     *
     * @param mixed $id
     * @param array $values
     * @return int
     */
    public function updateById(array $id, array $values)
    {
        try {
            $query_result = $this->client
                ->selectCollection($this->database, $this->collection)
                ->updateOne(array($this->document_id, DatabaseTypesProvider::objectID($id)), array('$set' => $values, '$currentDate' => array('lastModified' => true)));
            return $query_result->getModifiedCount();
        } catch (\MongoDB\Driver\Exception\BulkWriteException $ex) {
            throw new WriteException($ex->getMessage());
        }
    }

    /**
     * Delete item(s) from the store
     *
     * @param array $conditions
     * @return int
     */
    public function delete(array $conditions)
    {
        $query_result = $this->client
            ->selectCollection($this->database, $this->collection)
            ->deleteMany($conditions);
        return $query_result->getDeletedCount();
    }

    /**
     * Delete a document from the data store using the collection primary key
     *
     * @param mixed $id
     * @return int
     */
    public function deleteById(array $id)
    {
        $query_result = $this->client
            ->selectCollection($this->database, $this->collection)
            ->deleteOne(array($this->document_id, DatabaseTypesProvider::objectID($id)));
        return $query_result->getDeletedCount();
    }

    /**
     * Get item(s) from the store based on a certain criteria
     *
     * @param array $conditions
     * @return \Traversable
     */
    public function find(array $conditions = array(), array $options = null)
    {
        try {
            if (is_null($options) || empty($options)) {
                $query_result = $this->client
                    ->selectCollection($this->database, $this->collection)
                    ->find($conditions);
            } else {
                if ($conditions) {
                    $options = \array_merge(array(array('$match' => $conditions)), $options);
                }
                $query_result =  $this->findAggregate($options);
            }
            return Arr::iter($query_result, function ($item) {
                $item[$this->document_id] = $item[$this->document_id] instanceof ObjectId ? (string)$item[$this->document_id] : $item[$this->document_id];
                !isset($item[$this->getDatabaseTypes()->getCreatedAt()]) ?: $item[$this->getDatabaseTypes()->getCreatedAt()] = $this->getDatabaseTypes()->toDateString($item[$this->getDatabaseTypes()->getCreatedAt()]);
                !isset($item[$this->getDatabaseTypes()->getUpdatedAt()]) ?: $item[$this->getDatabaseTypes()->getUpdatedAt()] = $this->getDatabaseTypes()->toDateString($item[$this->getDatabaseTypes()->getUpdatedAt()]);
                return $item;
            });
        } catch (\Exception $th) {
            throw new ReadException($th->getMessage());
        }
    }

    /**
     * Find a document in the data store matching the given id
     *
     * @param mixed $id
     * @param array $options
     * @return \ArrayObject
     */
    public function findById($id, array $options = null)
    {
        try {
            return $this->client
                ->selectCollection($this->database, $this->collection)
                ->findOne(array($this->document_id, DatabaseTypesProvider::objectID($id)), $options);
        } catch (\Exception $th) {
            throw new ReadException($th->getMessage());
        }
    }

    /**
     * Query using the Mongodb Aggregation framework
     *
     * @param array $aggregations
     * @return \Traversable
     */
    public function findAggregate(array $aggregations)
    {
        return $this->client->selectCollection($this->database, $this->collection)->aggregate($aggregations);
    }

    /**
     * Return the data store client provider
     *
     * @return MongoClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the collection for the current transaction
     *
     * @param string $collection
     * @param string $document_id
     * @return INosqlConnection
     */
    public function setCollection($collection, $document_id)
    {
        $this->collection = $collection;
        $this->document_id = $document_id;
        return $this;
    }

    /**
     * Return the selected database collection
     *
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Return the primaryKey of the selected database collection
     *
     * @return string
     */
    public function getCollectionKey()
    {
        return $this->document_id;
    }

    /**
     * Set the database name
     *  @param string $database
     * @return static
     */
    public function setDatabase(? string $database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * Get the database server supported type provider
     *
     * @return IDatabaseTypes
     */
    public function getDatabaseTypes()
    {
        return new DatabaseTypesProvider;
    }

    /**
     * Class instances destructor
     */
    public function __destruct()
    {
        unset($this->client);
        $this->collection = null;
        $this->document_id = null;
    }
}
