<?php

namespace Drewlabs\Contracts\Data;

interface IDataProviderQueryResult extends \ArrayAccess
{
    /**
     * Returns the collection of data returned from the data provider query result
     *
     * @return array|mixed
     */
    public function getCollection();

    /**
     * Handler for setting instance items
     *
     * @param array|mixed $items
     * @return static
     */
    public function setCollection($items);
}
