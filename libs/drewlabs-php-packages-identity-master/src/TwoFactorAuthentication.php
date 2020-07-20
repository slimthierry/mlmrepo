<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Packages\Database\Extensions\IlluminateNoSqlModel as Model;

class TwoFactorAuthentication extends Model
{

    /**
  * Collection name
  *
  * @var string $entity
  */
    protected $entity = "two_factor_auths";

    /**
     * The name of the collection primary key or UUID
     *
     * @var string
     */
    protected $primaryKey = "_id";

    /**
     * Returns a unique name of an entity in request input mapping
     *
     * @var string
     */
    protected $entity_uniq_name = "two_factor_auth";

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Request mapping inputs
     *
     * @var array
     */
    protected $model_states = [];
}
