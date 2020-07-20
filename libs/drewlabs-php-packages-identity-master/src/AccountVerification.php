<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;

class AccountVerification extends Model
{

    /**
     * Collection name
     *
     * @var string $entity
     */
    protected $entity = "account_verifications";

    /**
     * The name of the collection primary key or UUID
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'code',
        'expiration_date',
    ];

    /**
     * Request mapping inputs
     *
     * @var array
     */
    protected $model_states = [];
}
