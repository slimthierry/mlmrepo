<?php

namespace Drewlabs\Core\Database\NoSql;

use Drewlabs\Core\Database\NoSql\Builder;
use Drewlabs\Core\Database\NoSql\Contracts\INosqlConnection;
use Drewlabs\Core\Database\NoSql\Mongo\Concerns\MongoFiltersBuilder;
use Drewlabs\Core\Database\NoSql\Mongo\Concerns\MongoGrammar;
use Drewlabs\Core\Database\NoSql\Mongo\MongoConnectionProvider;

class BuilderFactory
{
    /**
     * Generate a query builder based on the provided driver
     *
     * @param INosqlConnection $connection
     * @return Builder
     */
    public static function makeBuilder(INosqlConnection $connection)
    {
        if ($connection instanceof MongoConnectionProvider) {
            return static::getMongoBuilder($connection);
        }
        throw new \InvalidArgumentException('Connection not implemented.');
    }

    public static function getMongoBuilder(INosqlConnection $connection)
    {
        $builder = new Builder($connection);
        $builder->setQueryGrammar(new MongoGrammar())->setFiltersBuilder(new MongoFiltersBuilder());
        return $builder;
    }
}
