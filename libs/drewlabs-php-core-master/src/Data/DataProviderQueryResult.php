<?php

namespace Drewlabs\Core\Data;

use Drewlabs\Contracts\Data\IDataProviderQueryResult;

class DataProviderQueryResult implements IDataProviderQueryResult, \JsonSerializable
{

    /**
     * List of items that can be manipulated
     *
     * @var array
     */
    private $items;

    public function __construct($items =  null)
    {
        $items = !isset($items) ? [] : $items;
        $this->setCollection($items);
    }

    /**
     * {@inheritDoc}
     */
    public function getCollection()
    {
        return $this->offsetGet('data');
    }

    /**
     * {@inheritDoc}
     */
    public function setCollection($items)
    {
        $this->offsetSet('data', $items);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items) && isset($this->items[$offset]);
    }
    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return array_key_exists($offset, $this->items) ? $this->items[$offset] : null;
    }
    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }
    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function jsonSerialize()
    {
        return $this->items;
    }
}
