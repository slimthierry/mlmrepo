<?php

namespace Drewlabs\Packages\MLM\Services;

use Drewlabs\Contracts\Data\DataRepository\Repositories\IModelRepository;
use Drewlabs\Core\Data\Traits\DataProvider;
use Drewlabs\Packages\MLM\Services\Contracts\IExampleDataProvider;

class ExamplesDataProvider implements IExampleDataProvider
{
    use DataProvider;


    /**
     * @var IModelRepository
     */
    private $repository;

    public function __construct(IModelRepository $repository)
    {
        $this->repository = $repository;
    }
}