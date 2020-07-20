<?php

namespace Drewlabs\Packages\Identity\Services;

use Drewlabs\Contracts\Data\DataRepository\Repositories\IModelRepository;
use Drewlabs\Core\Data\Traits\DataProvider;

class RolesDataProvider implements \Drewlabs\Packages\Identity\Services\Contracts\IRolesDataProvider
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
