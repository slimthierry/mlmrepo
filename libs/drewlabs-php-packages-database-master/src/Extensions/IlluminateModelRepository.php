<?php

namespace Drewlabs\Packages\Database\Extensions;

use Drewlabs\Core\Data\Repositories\ModelRepository;
use Drewlabs\Contracts\Data\IModelable;
use Drewlabs\Core\Data\Exceptions\RepositoryException;
use Drewlabs\Packages\Database\Contracts\TransactionUtils;
use Drewlabs\Packages\Database\Traits\IlluminateModelRepository as IlluminateModelRepositoryTrait;

/**
 * @package Drewlabs\Packages\Database\Extensions
 * @deprecated 1.0.2
 * This class has been replaced with a more generic repository provider and will be remove in next update
 */
abstract class IlluminateModelRepository extends ModelRepository
{
    use IlluminateModelRepositoryTrait;
    /**
     * Database transictions utilities providers
     *
     * @var TransactionUtils
     */
    public $transactionUtils;

    public function __construct()
    {
        $this->model = $this->makeModel();
        $this->transactionUtils = app(\Drewlabs\Packages\Database\Contracts\TransactionUtils::class);
    }

    /**
     * @inheritDoc
     */
    protected function makeModel()
    {
        $model = app($this->getModel());
        if (!$model instanceof IModelable) {
            throw new RepositoryException("Class {$this->getModel()} must be an instance of " . IModelable::class);
        }
        return $model;
    }
}
