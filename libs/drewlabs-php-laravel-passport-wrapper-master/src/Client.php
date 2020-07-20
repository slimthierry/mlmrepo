<?php

namespace Drewlabs\Packages\PassportPHPLeagueOAuth;

use Laravel\Passport\Client as BaseClient;

class Client extends BaseClient
{

    /**
     * Indicates that the primary key is not an incrementable value
     *
     * @var boolean
     */
    public $incrementing = false;
    /**
     * Determine if the client should skip the authorization prompt.
     *
     * @return bool
     */
    public function skipsAuthorization()
    {
        return $this->firstParty();
    }
}
