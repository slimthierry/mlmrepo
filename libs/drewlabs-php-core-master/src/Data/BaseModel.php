<?php

namespace Drewlabs\Core\Data;

use Drewlabs\Contracts\Data\IParsable;
use Drewlabs\Contracts\Data\IModelable;

abstract class BaseModel implements IModelable, IParsable
{
    /**
     * Fillable storage columns of an entity
     *
     * @return array
     */
    protected $fillable = [];

    /**
     * Model state input maps
     *
     * @return array
     */
    protected $model_states = [];

    /**
     * Defines model relationships with other models
     *
     * @return array
     */
    protected $relations = [];

    /**
     * Defines a model entity unique name in the data storage
     */
    protected $entityIdentifier;

    /**
     * Returns the collection associated with the given model
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->table;
    }

    /**
     * Returns the collection unique identifying name
     *
     * @return string
     */
    public function getEntityUniqueName()
    {
        return $this->entityIdentifier;
    }

    /**
     * @inheritDoc
     */
    public function getFillables()
    {
        return $this->fillable ?? [];
    }

    /**
     * Returns the defined eloquent relationships of the given model
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->{'relations'} ? $this->{'relations'} : [];
    }

    /**
     * Returns the primaryKey of the given model
     *
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @inheritDoc
     */
    public function getModelStateMap()
    {
        return $this->model_states ?? [];
    }
}
