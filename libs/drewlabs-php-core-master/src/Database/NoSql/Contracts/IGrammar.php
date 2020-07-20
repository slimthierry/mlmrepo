<?php

namespace Drewlabs\Core\Database\NoSql\Contracts;

interface IGrammar extends INosqlQueryOperators
{
    /**
     * Convert primary key value a specific database type
     *
     * @param string|mixed $value
     * @return mixed;
     */
    public function toDbPrimaryKeyType($value);

    /**
     * Build a mongodb query conditions from user provided array of conditions
     * parameters
     *
     * @param array $conditions
     * @return IGrammar
     */
    public function prepareQuery(array $conditions);

    /**
     * Combine two query with a given operator
     *
     * @param string $operator
     * @param array|mixed $lhs
     * @param array|mixed $rhs
     * @return IGrammar
     */
    public function combine($operator, $lhs, $rhs);

    /**
     * Return the QueryBuilder OR operator
     *
     * @return string
     */
    public function getOrOperator();

    /**
     * Return the QueryBuilder AND operator
     *
     * @return string
     */
    public function getAndOperator();

    /**
     * Return the QueryBuilder IN operator
     *
     * @return string
     */
    public function getInOperator();

    /**
     * Query property getter
     *
     * @return array|mixed
     */
    public function getQuery();

    /**
     * Set the query property to a new value
     *
     * @param array|mixed $query
     * @return IGrammar
     */
    public function set($query);

    /**
     * Unset the query property
     *
     * @return void
     */
    public function unsetQuery();
}
