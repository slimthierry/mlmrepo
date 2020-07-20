<?php

namespace Drewlabs\Packages\Database\Traits;

use Drewlabs\Contracts\Data\IParsable;
use Drewlabs\Contracts\Data\DataRepository\Services\IModelAttributesParser;

trait IlluminateModelRepository
{

    /**
     * @override
     *
     * @inheritDoc
     *
     * @throws \RuntimeException
     */
    public function insert(array $values, bool $parse_inputs = false, $upsert = false, $upsertConditions = [])
    {
        try {
            // Call the parent update method
            $query_result = parent::insert($values, $parse_inputs, $upsert, $upsertConditions);
            $value = is_array($query_result) ? collect($query_result) : $query_result;
            return $value;
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     *
     * @override
     *
     * @inheritDoc
     *
     * @throws \RuntimeException
     */
    public function update(array $data, array $conditions = array(), bool $from_input_map = false, bool $mass_update =  false)
    {
        try {
            // Initialize database transaction
            $result = parent::update($data, $conditions, $from_input_map, $mass_update);
            // return result to caller
            return $result;
        } catch (\Exception $e) {
            // Cancel database transaction if any error
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * @override
     *
     * @inheritDoc
     */
    public function find(array $conditions = array(), $columns = array('*'))
    {
        $query_result = parent::find($conditions, $columns);
        return is_array($query_result) ? collect($query_result) : $query_result;
    }

    /**
     * @inheritDoc
     */
    public function modelPrimaryKey()
    {
        return $this->makeModel()->getPrimaryKey();
    }

    /**
     * @inheritDoc
     */
    protected function isInstanceOfIParsable()
    {
        return $this->makeModel() instanceof IParsable;
    }

    /**
     * Undocumented function
     *
     * @return IModelAttributesParser
     */
    protected function ModelAttributesParser()
    {
        return app(IModelAttributesParser::class);
    }
}
