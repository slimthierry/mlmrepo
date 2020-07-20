<?php

namespace Drewlabs\Contracts\Data;

use Drewlabs\Contracts\Data\IModelFilter;

/**
 * Interface IFiltersHandler
 * @package Drewlabs\Contracts\Data
 */
interface IFiltersHandler
{

 /**
  * @param bool $status
  * @return static
  */
    public function skipFilters($status = true);

    /**
     * @return mixed
     */
    public function getFilters();

    /**
     * @param IModelFilter $filter
     * @return static
     */
    public function getByFilter(IModelFilter $filter);

    /**
     * @param IModelFilter $filter
     * @return static
     */
    public function pushFilter(IModelFilter $filter);

    /**
     * @return static
     */
    public function applyFilter();
}
