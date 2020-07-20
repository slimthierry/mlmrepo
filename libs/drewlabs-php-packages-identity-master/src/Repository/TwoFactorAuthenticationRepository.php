<?php

namespace Drewlabs\Packages\Identity\Repository;

use Drewlabs\Packages\Database\Extensions\IlluminateModelRepository as ModelRepository;
use Drewlabs\Packages\Identity\TwoFactorAuthentication;

class TwoFactorAuthenticationRepository extends ModelRepository
{
    /**
     * @inheritDoc
     */
    public function getModel()
    {
        return TwoFactorAuthentication::class;
    }
}
