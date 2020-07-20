<?php

namespace Drewlabs\Packages\Database\Extensions;

use Drewlabs\Core\Data\Repositories\ModelRepository;
use Drewlabs\Contracts\Data\IModelable;
use Drewlabs\Core\Data\Exceptions\RepositoryException;
use Drewlabs\Utils\Str;
use Drewlabs\Packages\Database\Contracts\TransactionUtils;
use Drewlabs\Packages\Database\Traits\IlluminateModelRepository as IlluminateModelRepositoryTrait;

final class BaseIlluminateModelRepository extends ModelRepository
{
    use IlluminateModelRepositoryTrait;
    /**
     * Database transictions utilities providers
     *
     * @var TransactionUtils
     */
    public $transactionUtils;

    /**
     * String representation of the model class
     *
     * @var string
     */
    private $model_class;

    /**
     * Create an instance of the model repository class
     *
     * @param string $modelClass
     */
    public function __construct($modelClass = null)
    {
        if (isset($modelClass)) {
            $this->setModel($modelClass);
        }
        $this->transactionUtils = app(\Drewlabs\Packages\Database\Contracts\TransactionUtils::class);
    }

    public function setModel($modelClass)
    {
        $this->model_class = $modelClass;
        $this->validateModelClass();
        // Create the model instance from the passed configuration
        $this->model = $this->makeModel();
        return $this;
    }

    private function validateModelClass()
    {
        if (!(is_string($this->model_class)) || !($this->makeModel() instanceof IModelable)) {
            throw new RepositoryException("Constructor parameter must be an instance of string, must be a valid class that exists, and the class must be an instance of " . IModelable::class);
        }
    }

    /**
     * @inheritDoc
     */
    protected function makeModel()
    {
        return app($this->getModel());
    }

    /**
     * @inheritDoc
     */
    public function getModel()
    {
        return $this->model_class;
    }

    /**
     * Handle dynamic method calls on the model repository instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (is_string($method) && Str::contains($method, '__')) {
            $method = Str::contains($method, '::') ? explode('::', $method)[1] : $method;
            $items = explode('__', $method);
            // To be used to call the insert or update method on the model
            if ($items[0] === 'insert') {
                return $this->__insert_with_related(array_slice($items, 1), ...$parameters);
            } else if ($items[0] === 'update') {
                return $this->__update_with_related(array_slice($items, 1), ...$parameters);
            } else {
                throw new RepositoryException("Error . Undefined method " . $method . " on the model repository class");
            }
        }
    }

    /**
     * Provide functionnalities for inserting a model with it related
     *
     * @param string[] $relations
     * @param array $values
     * @param boolean $parse_inputs
     * @param boolean $upsert
     * @param array $conditions
     * @param boolean $mass_insert
     * @return mixed
     */
    public function __insert_with_related($relations, $values, $parse_inputs = false, $upsert = false, $conditions = [], $mass_insert = true)
    {
        $this->transactionUtils->startTransaction();
        try {
            $model = $this->insert($values, $parse_inputs, $upsert, $conditions);
            foreach ($relations as $i) {
                # code...
                if (method_exists($model, $i) && array_key_exists($i, $values)  && isset($values[$i])) {
                    $isArrayList = \array_filter($values[$i], 'is_array') === $values[$i];
                    if ($isArrayList) {
                        // If specified to insert item in mass, insert all entries in one query
                        if ($mass_insert) {
                            $model->{$i}()->createMany(array_map(function ($value) use ($model) {
                                return array_merge(
                                    $value,
                                    array(
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s')
                                    )
                                );
                            }, $values[$i]));
                        } else {
                            // Else insert each entry individually to provide user of the method
                            // the ability to listen for each insertion event
                            foreach ($values[$i] as $k) {
                                # code...
                                $model->{$i}()->create($k);
                            }
                        }
                    } else {
                        $model->{$i}()->create($values[$i]);
                    }
                }
            }
            $this->transactionUtils->completeTransaction();
            return $model;
        } catch (\Exception $e) {
            $this->transactionUtils->cancel();
            throw new \RuntimeException($e);
        }
    }

    /**
     * Provides functionnalities for updating a model with it related entries. Note, Only update using model
     * primary key is supported.
     *
     * @param string[] $relations
     * @param int|string $id
     * @param array $values
     * @param boolean $parse_inputs
     * @param boolean|null $upsert
     * @return int
     */
    public function __update_with_related($relations, $id, $values, $parse_inputs = false, $upsert = true)
    {
        try {
            $this->transactionUtils->startTransaction();
            // $relation_methods = array_slice($relations, 1);
            $updated = 0;
            $updated = $this->updateById($id, $values, $parse_inputs);
            $model = $this->findById($id, array($this->modelPrimaryKey()));
            if (!is_null($model)) {
                foreach ($relations as $i) {
                    # code...
                    if (method_exists($model, $i) && array_key_exists($i, $values) && isset($values[$i])) {
                        if ($upsert) {
                            $isArrayList = isset($values[$i][0]) && \array_filter($values[$i][0], 'is_array') === $values[$i][0];
                            if ($isArrayList) {
                                foreach ($values[$i] as $v) {
                                    # code...
                                    $this->updateOrCreateIfMatchCondition($model->{$i}(), $v);
                                }
                            } else {
                                $this->updateOrCreateIfMatchCondition($model->{$i}(), $values[$i]);
                            }
                        } else {
                            $isArrayList = isset($values[$i]) && \array_filter($values[$i], 'is_array') === $values[$i];
                            if ($isArrayList) {
                                $model->{$i}()->delete();
                                // Create many after deleting the all the related
                                $model->{$i}()->createMany(array_map(function ($value) use ($model) {
                                    return array_merge(
                                        $value,
                                        array(
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s')
                                        )
                                    );
                                }, $values[$i]));
                            } else {
                                $model->{$i}()->delete();
                                $model->{$i}()->create($values[$i]);
                            }
                        }
                    }
                }
            }
            $this->transactionUtils->completeTransaction();
            return $updated;
        } catch (\Exception $e) {
            $this->transactionUtils->cancel();
            throw new \RuntimeException($e);
        }
    }

    private function updateOrCreateIfMatchCondition($relation, $value)
    {
        if (count($value) === 2) {
            $relation->updateOrCreate($value[0], $value[1]);
        }
        if (count($value) === 1) {
            $relation->updateOrCreate($value[0], $value[0]);
        }
    }
}
