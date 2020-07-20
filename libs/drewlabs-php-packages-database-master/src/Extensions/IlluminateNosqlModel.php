<?php

namespace Drewlabs\Packages\Database\Extensions;

use Drewlabs\Core\Database\NoSql\NosqlModel as NoSqlBaseModel;
use Drewlabs\Contracts\Data\IParsable;

final class IlluminateNosqlModel extends NoSqlBaseModel implements IParsable
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
     * Dictionnary mapping of the fillable entries of the model and the request inputs
     *
     * @return array
     */
    public function getModelStateMap()
    {
        return $this->model_states ?? [];
    }

    /**
     * Returns the fillable properties of the given model
     *
     * @return array
     */
    public function getFillables()
    {
        return $this->fillable ?? [];
    }
}
