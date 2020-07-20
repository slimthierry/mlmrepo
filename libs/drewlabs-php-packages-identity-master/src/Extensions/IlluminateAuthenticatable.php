<?php

namespace Drewlabs\Packages\Identity\Extensions;

use Drewlabs\Contracts\Auth\Authenticatable as IAuthenticatable;
use Drewlabs\Core\Auth\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Drewlabs\Packages\Identity\Extensions\Authorizable;
use Drewlabs\Packages\Identity\Traits\IlluminateAuthenticatable as IlluminateAuthenticatableTrait;

final class IlluminateAuthenticatable extends User implements Authenticatable, IAuthenticatable, \Drewlabs\Contracts\Auth\IDrewlabsAuthorizable
{
    use HasApiTokensTrait;
    use IlluminateAuthenticatableTrait;
    use Authorizable;
    use IlluminateMustVerifyEmail;

    /**
     * List of fillable properties of the current object used to generate the json model
     *
     * @var array
     */
    protected $fillables = [
        'id',
        'username',
        'password',
        'is_active',
        'remember_token',
        'double_auth_active',
        'roles',
        'permissions',
        'user_info',
        'channels',
        'is_verified'
    ];

    /**
     * List of properties to not include in the client json output
     *
     * @var array
     */
    protected $guards = [
        'password',
    ];

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $serializer = app(\Drewlabs\Contracts\Auth\IAuthenticatableSerializer::class);
        return $serializer->serialize($this);
    }
}
