<?php

namespace Drewlabs\Packages\Database\Extensions;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Drewlabs\Contracts\Data\IParsable;
use Drewlabs\Contracts\Data\IModelable;
use Drewlabs\Contracts\Data\IRelatable;
use Drewlabs\Packages\Database\Traits\IlluminateBaseModel as IlluminateBaseModelTrait;


abstract class IlluminateBaseModel extends Eloquent implements IModelable, IParsable, IRelatable
{

    // Default implementation of search query on the model providers
    use IlluminateBaseModelTrait;
    use \Drewlabs\Packages\Database\Traits\RoutableModel;

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
     * Property for controlling if appended contents should be added to the model query json result
     *
     * @var boolean
     */
    protected $withoutAppends = false;

    /**
     * Checks if the current model has some relations
     *
     * @return boolean
     */
    protected function hasRelations()
    {
        return is_array($this->getRelations()) ?: false;
    }

    /**
     * @inheritDoc
     */
    public function add(array $items)
    {
        $isArrayList = \array_filter($items, 'is_array') === $items;
        if (!$isArrayList) {
            return $this->create($items);
        }
        return $this->insert($items);
    }

    /**
     * @inheritDoc
     */
    public function findWith(array $conditions, bool $relations = false)
    {
        if ($relations) {
            return $this->with($this->getRelations())->where($conditions);
        }
        return $this->where($conditions);
    }

    /**
     * @inheritDoc
     */
    public function getAll(bool $relations = false, array $columns = array('*'))
    {
        if ($relations) {
            return $this->with($this->getRelations())->get($columns);
        }
        return $this->get($columns);
    }

    /**
     * Get or select an item from the data store having a date matching some given value
     *
     * @param string $field
     * @param string $date
     * @param bool $relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function havingDate($field, $date, bool $relations = false)
    {
        if ($relations) {
            return $this->with($this->getRelations())->whereDate($field, $date);
        }
        return $this->whereDate($field, $date);
    }

    /**
     * Get or select an item from the data store having a date matching some given value
     *
     * @param string $field
     * @param string $min_date
     * @param string $max_date
     * @param bool $relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function havingDateBetween($field, $min_date, $max_date, bool $relations = false)
    {
        if ($relations) {
            return $this->with($this->getRelations())->whereDate($field, '>=', $min_date)->whereDate($field, '<=', $max_date);
        }
        return $this->whereDate($field, '>=', $min_date)->whereDate($field, '<=', $max_date);
    }

    /**
     * @inheritDoc
     */
    public function updateWith($conditions, array $update_values)
    {
        return $this->where($conditions)->update($update_values);
    }

    /**
     * @inheritDoc
     */
    public function deleteWith(array $conditions = null)
    {
        $model = is_null($conditions) ? $this : $this->where($conditions);
        if ($this->hasRelations()) {
            $deleted = 0;
            $model->get()->each(function ($value) use (&$deleted) {
                $deleted += $value->delete();
            });
            return $deleted;
        }
        return $model->delete();
    }

    /**
     * @inheritDoc
     */
    public function getEntity()
    {
        return $this->table;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getRelations()
    {
        return $this->{'relations'} ? $this->{'relations'} : [];
    }

    /**
     * @inheritDoc
     */
    public function getPrimaryKey()
    {
        return isset($this->primaryKey) ? $this->primaryKey : 'id';
    }

    /**
     * @inheritDoc
     */
    public function getModelStateMap()
    {
        return $this->model_states ?? [];
    }
}
