<?php

namespace Drewlabs\Core\EntityObject;

use Drewlabs\Contracts\Data\IModelable;
use Drewlabs\Contracts\EntityObject\IDtoObject;
use Drewlabs\Contracts\EntityObject\IDtoService;

class DtoService implements IDtoService
{
    /**
     *
     * @var string
     */
    private $class;

    /**
     * Object initializer. Implementation requires a class name for dynamically building dto object instance
     *
     * @param string $objecClass
     */
    public function __construct($objectClass = null)
    {
        if (isset($objectClass)) {
            $this->bindClass($objectClass);
        }
    }

    /**
     * @inheritDoc
     */
    public function bindClass($objectClass)
    {
        $this->class = $objectClass;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toObject($model, $loadAll = false)
    {
        if (is_object($model) && !($model instanceof IModelable)) {
            return $this->createTemplateClass()->fromStdClass($model);
        }
        return $this->createTemplateClass()->copyWith(is_array($model) ? $model : $model->toArray(), $loadAll);
    }

    /**
     * @inheritDoc
     */
    public function objectToModel($obj)
    {
        if (is_object($obj) && !($obj instanceof IDtoObject)) {
            $obj = $this->createTemplateClass()->fromStdClass($obj);
        }
        return $obj->toModel();
    }

    /**
     * @inheritDoc
     */
    public function objectToModelList(array $list)
    {
        return array_map(function($i) {
            # code...
            return $this->objectToModel($i);
        }, $list);
    }

    /**
     * @inheritDoc
     */
    public function toObjectList(array $values, $loadAll = false)
    {
        return array_map(function($i) use ($loadAll){
            # code...
            return $this->toObject($i, $loadAll);
        }, $values);
    }

    /**
     * Create binding class instance
     *
     * @return IDtoObject
     */
    private function createTemplateClass()
    {
        $this->ensureClassBinding();
        $tmp = (new $this->class);
        $this->ensureObjectType($tmp);
        return $tmp;
    }

    private function ensureClassBinding()
    {
        if (is_null($this->class)) {
            throw new \RuntimeException('Bad method call, $class property is not set, try binding Dto object before call this method');
        }
    }

    private function ensureObjectType($obj)
    {
        if (!($obj instanceof IDtoObject)) {
            throw new \RuntimeException("Class passed to contructor must be an instance of \Drewlabs\Contracts\EntityObject\IDtoObject, " . get_class($obj) . " object given");
        }
    }
}
