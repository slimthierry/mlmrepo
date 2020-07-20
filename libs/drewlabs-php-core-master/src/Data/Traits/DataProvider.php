<?php

namespace Drewlabs\Core\Data\Traits;

trait DataProvider
{

    /**
     * {@inheritDoc}
     */
    public function create(array $attributes, $params = [])
    {
        $params = $this->parseProviderCreateHandlerParams($params);
        return $this->repository->resetScope()->{$params['method']}($attributes, true, $params['upsert'], $params['upsert_conditions']);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($query, $shouldMassDelete = false)
    {
        if (is_array($query)) {
            return $this->repository->resetScope()->pushFilter(
                app(\Drewlabs\Contracts\Data\IModelFilter::class)->setQueryFilters(
                    $query
                )
            )->delete(array(), $shouldMassDelete);
        }
        return $this->repository->resetScope()->deleteById($query);
    }

    /**
     * {@inheritDoc}
     */
    public function get($query = [], $columns = array('*'), $relationQuery = false, $shouldPaginate = false, $limit = null)
    {
        if (!is_array($query)) {
            return $this->getById($query);
        }
        $relationFn = 'queryRelation';
        if ((!is_array($relationQuery) && !is_bool($relationQuery))) {
            $relationQuery = false;
        }
        if (is_array($relationQuery)) {
            $relationFn = 'loadWith';
        }
        return $shouldPaginate ?  $this->repository->resetScope()->pushFilter(
            app(\Drewlabs\Contracts\Data\IModelFilter::class)
                ->setQueryFilters(is_null($query) ? [] : $query)
        )->{$relationFn}($relationQuery)->paginate($limit) : new \Drewlabs\Core\Data\DataProviderQueryResult(
            $this->repository->resetScope()->pushFilter(
                app(\Drewlabs\Contracts\Data\IModelFilter::class)->setQueryFilters($query)
            )->{$relationFn}($relationQuery)->find(array(), $columns)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getById($id)
    {
        if (is_numeric($id) || is_string($id)) {
            return $this->repository->resetScope()->findById($id);
        }
        throw new \RuntimeException("Bad query parameter, valid numeric argument");
    }

    /**
     * {@inheritDoc}
     */
    public function modify($query, array $attributes, $params = [])
    {
        $params = $this->parseProviderUpdateHandlerParams($params);
        if (is_array($query)) {
            return $this->repository->resetScope()->pushFilter(
                app(\Drewlabs\Contracts\Data\IModelFilter::class)->setQueryFilters($query)
            )->update($attributes, array(), true, $params['should_mass_update']);
        }
        return $this->repository->resetScope()->{$params['method']}($query, $attributes, true, $params['upsert']);
    }

    /**
     * Undocumented function
     *
     * @param array|\Drewlabs\Contracts\Data\IDataProviderHandlerParams $params
     * @return void
     */
    protected function parseProviderUpdateHandlerParams($params)
    {
        $value = $params instanceof \Drewlabs\Contracts\Data\IDataProviderHandlerParams ? $params->getParams() : (is_array($params) ? $params : []);
        $value['method'] = !isset($value['method']) ? 'updateById' : (!is_string($value['method']) ? 'updateById' : $value['method']);
        $value['upsert'] = !isset($value['upsert']) ? false : (!is_bool($value['upsert']) ? false : $value['upsert']);
        $value['should_mass_update'] = !isset($value['should_mass_update']) ? false : (!is_bool($value['should_mass_update']) ? false : $value['should_mass_update']);
        return $value;
    }

    /**
     * Undocumented function
     *
     * @param array|\Drewlabs\Contracts\Data\IDataProviderHandlerParams $params
     * @return void
     */
    protected function parseProviderCreateHandlerParams($params)
    {
        $value = $params instanceof \Drewlabs\Contracts\Data\IDataProviderHandlerParams ? $params->getParams() : (is_array($params) ? $params : []);
        $value['upsert'] = !isset($value['upsert']) ? false : (!is_bool($value['upsert']) ? false : $value['upsert']);
        $value['method'] = !isset($value['method']) ? 'insert' : (!is_string($value['method']) ? 'insert' : $value['method']);
        $value['upsert_conditions'] = !isset($value['upsert_conditions']) ? [] : (!is_array($value['upsert_conditions']) ? [] : $value['upsert_conditions']);
        return $value;
    }
}
