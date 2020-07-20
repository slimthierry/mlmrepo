<?php

namespace Drewlabs\Packages\Database\Extensions;

use Drewlabs\Contracts\Data\IModelFilter;
use Drewlabs\Contracts\Data\IModelable;
use Drewlabs\Contracts\Data\DataRepository\Repositories\IModelRepository;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Builder;

class CustomQueryCriteria implements IModelFilter
{

    /**
     * Dictionnary of model method to query filters
     *
     * @var array
     */
    protected $query_criteria;

    /**
     * @var Eloquent|Model|Builder
     */
    protected $model;

    private $query_methods = [
        'where',
        'whereHas',
        'whereDoesntHave',
        'whereDate',
        'has',
        'doesntHave',
        'orWhere',
        'whereIn',
        'whereNotIn',
        'orderBy',
        'groupBy',
        'skip',
        'take'
    ];

    public function __construct(array $filter_list = null)
    {
        if (isset($filter_list)) {
            $this->setQueryFilters($filter_list);
        }
    }

    /**
     * @inheritDoc
     */
    public function apply($model, IModelRepository $repository)
    {
        $_model = clone $model;
        foreach (array_keys($this->query_criteria) as $v) {
            # code...
            $method = 'apply' . ucfirst($v) . 'Query';
            if (method_exists($this, $method)) {
                $_model = \call_user_func_array(array($this, $method), array($_model, $this->query_criteria));
            }
        }
        return $_model;
    }

    /**
     * apply a where query to the model
     *
     * @param Eloquent|IModelable|Builder $model
     * @param array|callback $criteria
     * @return Eloquent|IModelable|Builder
     */
    private function applyWhereQuery($model, $criteria)
    {
        if (array_key_exists('where', $criteria) && !\is_null($criteria['where'])) {
            $isArrayList = \array_filter($criteria['where'], 'is_array') === $criteria['where'];
            if ($isArrayList) {
                $model = $model->where($criteria['where']);
            } else {
                $model = $model->where(...$criteria['where']);
            }
        }
        return $model;
    }

    /**
     * apply a where query to the model
     *
     * @param Eloquent|IModelable|Builder $model
     * @param array $criteria
     * @return Eloquent|IModelable|Builder
     */
    private function applywhereHasQuery($model, $criteria)
    {
        if (array_key_exists('whereHas', $criteria) && !\is_null($criteria['whereHas'])) {
            $isArrayList = \array_filter($criteria['whereHas'], 'is_array') === $criteria['whereHas'];
            if ($isArrayList) {
                foreach ($criteria['whereHas'] as $value) {
                    # code...
                    $model = $model->whereHas($value[0], $value[1]);
                }
            } else {
                $model = $model->whereHas($criteria['whereHas'][0], $criteria['whereHas'][1]);
            }
        }
        return $model;
    }

    /**
     * apply a where query to the model
     *
     * @param Eloquent|IModelable|Builder $model
     * @param array $criteria
     * @return Eloquent|IModelable|Builder
     */
    private function applyWhereDoesntHaveQuery($model, $criteria)
    {
        if (array_key_exists('whereDoesntHave', $criteria) && !\is_null($criteria['whereDoesntHave'])) {
            $isArrayList = \array_filter($criteria['whereDoesntHave'], 'is_array') === $criteria['whereDoesntHave'];
            if ($isArrayList) {
                foreach ($criteria['whereDoesntHave'] as $value) {
                    # code...
                    $model = $model->whereDoesntHave(...$value);
                }
            } else {
                $model = $model->whereDoesntHave(...$criteria['whereDoesntHave']);
            }
        }
        return $model;
    }

    /**
     * apply a whereDate query to the model
     *
     * @param Eloquent|IModelable|Builder $model
     * @param array $criteria
     * @return Eloquent|IModelable|Builder
     */
    private function applyWhereDateQuery($model, $criteria)
    {
        if (array_key_exists('whereDate', $criteria) && !\is_null($criteria['whereDate'])) {
            $isArrayList = \array_filter($criteria['whereDate'], 'is_array') === $criteria['whereDate'];
            if ($isArrayList) {
                foreach ($criteria['whereDate'] as $value) {
                    # code...
                    $model = $model->whereDate(...$value);
                }
            } else {
                $model = $model->whereDate(...$criteria['whereDate']);
            }
        }
        return $model;
    }

    /**
     * Apply a has query
     *
     * @param Eloquent|IModelable|Builder $model
     * @param array $criteria
     * @return Eloquent|IModelable|Builder
     */
    private function applyHasQuery($model, $criteria)
    {
        if (array_key_exists('has', $criteria) && !\is_null($criteria['has'])) {
            if (is_string($criteria['has'])) {
                $model = $model->has($criteria['has']);
            }
            if (is_array($criteria['has'])) {
                foreach ($criteria['has'] as $value) {
                    # code...
                    $model = $model->has($value);
                }
            }
        }
        return $model;
    }

    /**
     * Apply a has query
     *
     * @param Eloquent|IModelable|Builder $model
     * @param array $criteria
     * @return Eloquent|IModelable|Builder
     */
    private function applyDoesntHaveQuery($model, $criteria)
    {
        if (array_key_exists('doesntHave', $criteria) && !\is_null($criteria['doesntHave'])) {
            if (is_string($criteria['doesntHave'])) {
                $model = $model->doesntHave($criteria['doesntHave']);
            }
            if (is_array($criteria['doesntHave'])) {
                foreach ($criteria['doesntHave'] as $value) {
                    # code...
                    $model = $model->doesntHave($value);
                }
            }
        }
        return $model;
    }

    /**
     * apply an orWhere query to the model
     *
     * @param Eloquent|IModelable|Builder $model
     * @param array $criteria
     * @return Eloquent|IModelable|Builder
     */
    private function applyOrWhereQuery($model, $criteria)
    {
        if (array_key_exists('orWhere', $criteria) && !\is_null($criteria['orWhere'])) {
            $isArrayList = \array_filter($criteria['orWhere'], 'is_array') === $criteria['orWhere'];
            if ($isArrayList) {
                $model = $model->orWhere($criteria['orWhere']);
            } else {
                $model = $model->orWhere(...$criteria['orWhere']);
            }
        }
        return $model;
    }

    /**
     * apply a whereIn query to the model
     *
     * @param Eloquent|IModelable|Builder $model
     * @param array $criteria
     * @return Eloquent|IModelable|Builder
     */
    private function applyWhereInQuery($model, array $criteria)
    {
        if (array_key_exists('whereIn', $criteria) && !\is_null($criteria['whereIn'])) {
            $model = $model->whereIn($criteria['whereIn'][0], $criteria['whereIn'][1]);
        }
        return $model;
    }

    /**
     * apply a whereNotIn query to the model
     *
     * @param Eloquent|IModelable|Builder $model
     * @param array $criteria
     * @return Eloquent|IModelable|Builder
     */
    private function applyWhereNotInQuery($model, array $criteria)
    {
        if (array_key_exists('whereNotIn', $criteria) && !\is_null($criteria['whereNotIn']) && (count($criteria['whereNotIn']) >= 2) ) {
            $model = $model->whereNotIn($criteria['whereNotIn'][0], $criteria['whereNotIn'][1]);
        }
        return $model;
    }

    /**
     * apply an orderBy query to the model
     *
     * @param Eloquent|IModelable|Builder $model
     * @param array $criteria
     * @return Eloquent|IModelable|Builder
     */
    private function applyOrderByQuery($model, array $criteria)
    {
        if (array_key_exists('orderBy', $criteria) && !\is_null($criteria['orderBy']) && $criteria['orderBy']) {
            $model = $model->orderBy($criteria['orderBy']['by'], $criteria['orderBy']['order']);
        }
        return $model;
    }

    /**
     * Apply group by query on the provided model instance
     *
     * @param Eloquent|IModelable|Builder $model
     * @param array[]|string $criteria
     * @return Eloquent|IModelable|Builder
     */
    private function applyGroupByQuery($model, array $criteria)
    {
        if (
            array_key_exists('groupBy', $criteria)
            && !\is_null($criteria['groupBy']) &&
            (is_array($criteria['groupBy']) || is_string($criteria['groupBy']))
        ) {
            if (is_string($criteria['groupBy'])) {
                $model = $model->groupBy($criteria['groupBy']);
            } else {
                $model = $model->groupBy(...$criteria['groupBy']);
            }
        }
        return $model;
    }

    /**
     * apply an skip query to the model
     *
     * @param Eloquent|IModelable|Builder $model
     * @param array $criteria
     * @return Eloquent|IModelable|Builder
     */
    private function applySkipQuery($model, array $criteria)
    {
        if (array_key_exists('skip', $criteria) && !\is_null($criteria['skip'])) {
            $model = $model->skip($criteria['skip']);
        }
        return $model;
    }

    /**
     * apply an skip query to the model
     *
     * @param Eloquent|IModelable|Builder $model
     * @param array $criteria
     * @return Eloquent|IModelable|Builder
     */
    private function applyTakeQuery($model, array $criteria)
    {
        if (array_key_exists('take', $criteria) && !\is_null($criteria['take'])) {
            $model = $model->take($criteria['take']);
        }
        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function setQueryFilters(array $list)
    {
        $this->query_criteria = $list;
        return $this;
    }

    /**
     * @var string
     */
    const WHERE = 'where';
    /**
     * @var string
     */
    const ORWHERE = 'orWhere';
    /**
     * @var string
     */
    const WHEREIN = 'whereIn';
    /**
     * @var string
     */
    const WHERENOTIN = 'whereNotIn';
    /**
     * @var string
     */
    const ORDER_BY = 'orderBy';
    /**
     * @var string
     */
    const SKIP = 'skip';
    /**
     * @var string
     */
    const TAKE = 'take';
}
