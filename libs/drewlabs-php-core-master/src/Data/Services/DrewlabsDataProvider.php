<?php

namespace Drewlabs\Core\Data\Services;

use Drewlabs\Contracts\Data\DataRepository\Repositories\IModelRepository;
use Drewlabs\Contracts\Data\IDataProvider;
use Drewlabs\Core\Data\Traits\DataProvider;

final class DrewlabsDataProvider implements IDataProvider
{
    use DataProvider;

    /**
     *
     * @var IModelRepository
     */
    private $repository;

    public function __construct(IModelRepository $repository)
    {
        $this->repository = $repository;
    }
}
