<?php

namespace Drewlabs\Packages\Identity\Repository;

use Drewlabs\Packages\Database\Extensions\IlluminateModelRepository as ModelRepository;
use Drewlabs\Packages\Identity\AccountVerification;

class AccountVerificationRepository extends ModelRepository
{
    /**
     * @inheritDoc
     */
    public function getModel()
    {
        return AccountVerification::class;
    }
}
