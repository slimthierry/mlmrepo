<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Contracts\Auth\IDrewlabsNotifiable;
use Drewlabs\Contracts\Auth\IUserModel as UserModelContract;
use Drewlabs\Contracts\Auth\IVerifiable as VerifiableContract;
use Drewlabs\Core\Auth\Traits\UserModel;
use Drewlabs\Core\Auth\Traits\VerifiableTrait;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel;
use Drewlabs\Packages\Identity\Traits\DrewlabsNotifiable;
use Drewlabs\Packages\Identity\Traits\IdentityUser;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends IlluminateBaseModel implements UserModelContract, IDrewlabsNotifiable, VerifiableContract
{
    use UserModel;
    use IdentityUser;
    use SoftDeletes;
    use DrewlabsNotifiable;
    use VerifiableTrait;

    /**
     * Related table name
     *
     * @var string
     */
    protected $table = "users";
    /**
     * User primary key field
     *
     * @var string
     */
    protected $primaryKey = "user_id";

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = [
        "user_name",
        "user_password",
        "is_active",
        "remember_token",
        "double_auth_active",
        "lock_enabled",
        "lock_expired_at",
        "login_attempts",
        "created_by",
        "is_verified"
    ];

    protected $hidden = [
        'user_password',
        'remember_token'
    ];

    protected $guarded = [
        "user_password",
        "remember_token",
        "created_by"
    ];


    public function __get($key)
    {
        switch ($key) {
            case "active_field":
                return "is_active";
            case "password_field":
                return "user_password";
            case "remember_token_field":
                return "remember_token";
            case "double_auth_active_field":
                return "double_auth_active";
            default:
                return parent::__get($key);
        }
    }
}
