<?php

namespace Drewlabs\Packages\Http\Controllers;

use Illuminate\Contracts\Container\Container as Application;
use Drewlabs\Contracts\Data\DataRepository\Repositories\IModelRepository as ModelRepository;
use Drewlabs\Contracts\Data\IFiltersHandler;
use Drewlabs\Contracts\Data\IRelatableRepository;

abstract class RessourcesBaseController extends ApiController
{
    /**
     * @var ModelRepository|IFiltersHandler|IRelatableRepository
     */
    protected $repository;

    /**
     *
     * @param Application $app
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }
}
