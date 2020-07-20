<?php

namespace Drewlabs\Core\Database\NoSql\Contracts;

interface INosqlQueryOperators
{
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
}
