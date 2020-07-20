<?php

namespace Drewlabs\Core\Data\Repositories;

use Drewlabs\Contracts\Data\DataRepository\Repositories\IModelRepository;
use Drewlabs\Contracts\Data\IFiltersHandler;
use Drewlabs\Contracts\Data\IModelable;
use Drewlabs\Core\Data\Traits\RepositoryFilter;
use Drewlabs\Contracts\Data\DataRepository\Services\IModelAttributesParser;
use Drewlabs\Contracts\Data\IParsable;
use Drewlabs\Core\Data\Exceptions\RepositoryException;
use Drewlabs\Contracts\Data\IRelatableRepository;

abstract class ModelRepository implements IModelRepository, IFiltersHandler, IRelatableRepository
{
    use RepositoryFilter;

    /**
     * Model instance variable
     *
     * @var IParsable|IModelable
     */
    protected $model;

    /**
     * @var bool
     */
    protected $query_model_relation = false;

    /**
     * @var bool
     */
    protected $skip_filters;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @inheritDoc
     */
    public function insert(array $values, bool $parse_inputs = false, $upsert = false, $upsertConditions = [])
    {
        if ($parse_inputs) {
            if (!$this->isInstanceOfIParsable()) {
                throw new RepositoryException("Class {$this->getModel()} must be an instance of " . IParsable::class);
            }
            $values = $this->ModelAttributesParser()->setModel($this->makeModel())->setModelInputState($values)->getModelInputState();
        }
        return $upsert ? $this->model->{'updateOrCreate'}(
                !empty($upsertConditions) ? $upsertConditions : $values,
                $values
            ) :
            $this->model->add($values);
    }

    /**
     * @inheritDoc
     */
    public function insertMany(array $values, $parse_inputs = true)
    {
        if (\array_filter($values, 'is_array') === $values) {
            $list = array();
            // Loop through individual elements and parse the model state
            foreach ($values as $input) {
                // Set timestamps values in case of bulk assignement
                if ($parse_inputs && $this->isInstanceOfIParsable()) {
                    // $model_mapper = $this->ModelAttributesParser();
                    $input = $this->ModelAttributesParser()->setModel($this->makeModel())->setModelInputState($input)->getModelInputState();
                }
                $insertion_value = array_merge($input, array('updated_at' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s')));
                $list[] = $insertion_value;
            }
            return $this->model->{"insert"}($list);
        }
        throw new RepositoryException(__METHOD__ . ' requires an list of list items for insertion');
    }

    /**
     * @inheritDoc
     */
    public function all($columns = array('*'))
    {
        return $this->makeModel()->getAll($this->query_model_relation, $columns);
    }

    /**
     * @inheritDoc
     */
    public function paginate($perPage = 20, $columns = array('*'))
    {
        $this->applyFilter();
        if ($this->query_model_relation) {
            $this->model = $this->model->{"with"}($this->makeModel()->{"getRelations"}());
        }
        $list = $this->model->paginate($perPage, $columns);
        // Reset the scope on each paginate calls
        $this->resetScope();
        // Return the result to the user
        return $list;
    }

    /**
     * @inheritDoc
     */
    public function find(array $conditions = array(), array $columns = array('*'))
    {
        $model_relations = $this->makeModel()->{"getRelations"}();
        if (empty($conditions)) {
            $this->applyFilter();
            $list = ($this->query_model_relation && method_exists($this->model, 'with') && !empty($model_relations)) ? $this->model->{"with"}($model_relations)->get($columns) : $this->model->{"get"}($columns);
        } else {
            if ($this->model instanceof IModelable) {
                $list = $this->model->findWith($conditions, $this->query_model_relation)->get($columns);
            } else {
                $list = $this->model->{"with"}($model_relations)->where($conditions);
            }
        }
        // Reset the scope when the find call get completed
        $this->resetScope();
        // Return the result to the user
        return $list;
    }

    /**
     * @inheritDoc
     */
    public function findById($id, array $columns = array('*'))
    {
        return $this->queryRelation(true)->find(array(array($this->modelPrimaryKey(), $id)), $columns)->first();
    }

    /**
     * @inheritDoc
     */
    public function updateById($id, array $data, bool $parse_inputs = true)
    {
        $result = $this->queryRelation(false)->find(array(array($this->modelPrimaryKey(), $id)))->first();
        if ($result) {
            if ($parse_inputs) {
                $data = $this->parseInputValues($data);
            }
            $result->update($data);
            return 1;
            // TODO: Returns the updated value in the future implementations
        }
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function update(array $data, array $conditions = array(), bool $parse_inputs = true, bool $mass_update =  false)
    {
        if ($parse_inputs) {
            $data = $this->parseInputValues($data);
        }
        if (empty($conditions)) {
            $this->applyFilter();
            $update_count = 0;
            if ($mass_update) {
                // If should mass update the model, mass update it
                $update_count = $this->model->{"update"}($data);
            } else {
                //  Get the list of models that matches the query
                $list = $this->model->{"get"}();
                // Collect the list if it is an array
                $list = is_array($list) ? collect($list) : $list;
                // Loop through all the item in the list and update their field
                $list->each(function ($value) use (&$update_count, $data) {
                    // Then save the model to the database
                    $value->update($data);
                    $update_count++;
                });
            }
            // Reset the scope when the update call get completed
            $this->resetScope();
            return $update_count;
        }
        return $this->model->updateWith($conditions, $data);
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        $result = $this->queryRelation(false)->find(array(array($this->modelPrimaryKey(), $id)))->first();
        if ($result) {
            // Then save the model to the database
            $result->delete();
            return 1;
        }
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function delete(array $conditions = array(), bool $mass_delete =  false)
    {
        if (empty($conditions)) {
            $this->applyFilter();
            // Perform a mass delete operation
            if ($mass_delete) {
                return $this->model->{"delete"}();
            }
            // Perform a mass delete on each element of the list of model
            $deleted = 0;
            $list = $this->model->{"get"}();
            $list = is_array($list) ? collect($list) : $list;
            $list->each(function ($value) use (&$deleted) {
                $deleted += $value->delete();
            });
            // Reset the scope when the delete call get completed
            $this->resetScope();
            return $deleted;
        }
        return $this->model->deleteWith($conditions);
    }

    /**
     * {@inheritDoc}
     */
    public function queryRelation(bool $value = true)
    {
        $this->query_model_relation = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function loadWith($relations)
    {
        $this->model = $this->model->{'with'}($relations);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resetScope()
    {
        $this->skipFilters(false);
        // Set class properties to their default values
        $this->model = $this->makeModel();
        $this->filters = [];
        $this->query_model_relation = false;
        return $this;
    }

    private function parseInputValues(array $values)
    {
        if (!$this->isInstanceOfIParsable()) {
            throw new RepositoryException("Class {$this->getModel()} must be an instance of " . IParsable::class);
        }
        $values = $this->ModelAttributesParser()->setModel($this->makeModel())->setModelInputState($values)->getModelInputState();
        return $values;
    }

    /**
     * Checks if a model is an instance of IParsable class
     *
     * @return boolean
     */
    abstract protected function isInstanceOfIParsable();

    /**
     * Return model collection|table unique identifier key
     *
     * @return string
     */
    abstract public function modelPrimaryKey();

    /**
     * Set the current repository model
     *
     * @return IParsable|IModelable
     */
    abstract protected function makeModel();

    /**
     * {@inheritDoc}
     */
    abstract public function getModel();

    /**
     * Create mappable model service class
     *
     * @return IModelAttributesParser
     */
    abstract protected function ModelAttributesParser();
}
