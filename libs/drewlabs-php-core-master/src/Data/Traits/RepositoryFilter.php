<?php

namespace Drewlabs\Core\Data\Traits;

use Drewlabs\Contracts\Data\IModelFilter;

trait RepositoryFilter
{

    /**
     * @inheritDoc
     */
    public function skipFilters($status = true)
    {
        $this->skip_filters = $status;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param mixed $filter
     * @return static
     */
    public function getByFilter($filter)
    {
        $this->model = $filter->apply($this->model, $this);
        return $this;
    }

    /**
     * @param IModelFilter $filter
     * @return static
     */
    public function pushFilter(IModelFilter $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * @return static
     */
    public function applyFilter()
    {
        if ($this->skip_filters === true) {
            return $this;
        }

        foreach ($this->getFilters() as $filter) {
            if (method_exists($filter, 'apply')) {
                $this->model = $filter->apply($this->model, $this);
            }
        }
        return $this;
    }
}
