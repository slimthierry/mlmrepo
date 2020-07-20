<?php

namespace Drewlabs\Packages\Database\Traits;

use Drewlabs\Utils\Str;
use Illuminate\Http\Request;


trait IlluminateBaseModel
{
    /**
     * Get the query parameters that exists on the class fillable property and trie building model filter query
     * @param Request $request
     * @return array
     */
    public function parseRequestQueryFilters(Request $request)
    {
        $filters = [];
        if ($request->has($this->primaryKey) && !\is_null($request->get($this->primaryKey))) {
            $filters['where'][] = array($this->primaryKey, $request->get($this->primaryKey));
        }
        foreach ($request->all() as $key => $value) {
            # code...
            $searchable = array_merge($this->fillable, $this->guarded);
            if (!empty($value)) {
                if (in_array($key, $searchable)) {
                    $filters['orWhere'][] = array($key, 'like', '%' . $value . '%');
                } else if (Str::contains($key, ['__'])) {
                    $exploded = \explode('__', $key);
                    $relation = $exploded[0];
                    $column = $exploded[1];
                    if (method_exists($this, $relation) && !is_null($column)) {
                        $filters['whereHas'][] = array($relation, function ($query) use ($value, $column) {
                            if (is_array($value)) {
                                $query->whereIn($column, $value);
                                return;
                            }
                            $operator = is_numeric($value) || is_bool($value) ? '=' : 'like';
                            $value = is_numeric($value) ? $value : '%' . $value . '%';
                            $query->where($column, $operator, $value);
                        });
                    }
                }
            }
        }
        return $filters;
    }

    protected function getArrayableAppends()
    {
        $route = $this->getIndexRoute();
        if ($this->withoutAppends) {
            return !is_null($route) && is_string($route) ? array('_link') : array();
        }
        return array_merge(parent::getArrayableAppends(), isset($route) && is_string($route) ? array('_link') : array());
    }

    /**
     * Set the value of the withoutAppends property
     *
     * @param bool $value
     * @return static
     */
    public function setWithoutAppends($value)
    {
        $this->withoutAppends = $value;
        return $this;
    }
}
