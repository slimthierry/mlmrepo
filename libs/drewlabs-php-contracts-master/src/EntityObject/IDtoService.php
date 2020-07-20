<?php

namespace Drewlabs\Contracts\EntityObject;

use Drewlabs\Contracts\Data\IModelable;

interface IDtoService
{
    /**
     * Bind [[IDtoObject]] class string to the current service
     *
     * @param string $objectClass
     * @return static
     */
    public function bindClass($dtoClassName);

    /**
     * Generate a [[IDtoObject]] from a [[IModelable|\stdClass|array]] instance
     *
     * @param IModelable|\stdClass|array $model
     * @param bool $loadAll
     * @return IDtoObject
     */
    public function toObject($model, $loadAll = false);

    /**
     * Generate a [[IModelable]] from a [[IDtoObject|IDtoObject|\stdClass|array]] instance
     *
     * @param IDtoObject|\stdClass|array $model
     * @return IModelable
     */
    public function objectToModel($obj);

    /**
     * Build a [[IModelable[]]] from a [[IDtoObject[]]] instance
     *
     * @param IDtoObject[] $model
     * @return IModelable[]
     */
    public function objectToModelList(array $obj);

    /**
     * Build a list of [[IDtoObject[]]] from a [[IModelable[]]] params
     *
     * @param IModelable[] $values
     * @param bool $loadAll
     * @return IDtoObject[]
     */
    public function toObjectList(array $values, $loadAll = false);
}
