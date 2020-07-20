<?php

namespace Drewlabs\Packages\Identity\Traits;

use Drewlabs\Contracts\Auth\Authenticatable;
use Drewlabs\Packages\Identity\RoleUser;
use Drewlabs\Packages\Identity\UserInfo;

/**
 * This class must contains extension functionnality to be added to User model class
 */
trait IdentityUser
{
    /**
     * Defines relation between this model and the association model with roles model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_roles()
    {
        if (!\is_null(\config('drewlabs_identity.models.role_user.class', null)) && !\is_null(\config('drewlabs_identity.models.role_user.foreign_key', null))) { // foreign_key
            return $this->hasMany(\config('drewlabs_identity.models.role_user.class'), \config('drewlabs_identity.models.role_user.foreign_key'));
        }
        return $this->hasMany(RoleUser::class, RoleUser::getUserIdFieldName());
    }

    /**
     * Defines relation between this model and the association model with roles model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        if (
            !\is_null(\config('drewlabs_identity.models.role_user.class', null)) &&
            !\is_null(\config('drewlabs_identity.models.role.class', null))
        ) {
            $rolePrimaryKey = \config('drewlabs_identity.models.role.primaryKey', 'id');
            $roleForeignKey = \config('drewlabs_identity.models.role.foreign_key', 'role_id');
            $roleClass = \config('drewlabs_identity.models.role.class');
            $roleUserClass = \config('drewlabs_identity.models.role_user.class');
            $roleUserModel = app($roleUserClass);
            return $this->belongsToMany(
                $roleClass,
                !is_null($roleUserModel->getEntity()) ? $roleUserModel->getEntity() : $roleUserModel->getTable(),
                "user_id",
                $roleForeignKey,
                "user_id",
                $rolePrimaryKey
            )
                ->using($roleUserClass)
                ->withPivot([]);
        }
        return $this->belongsToMany(
            \Drewlabs\Packages\Identity\Role::class,
            (new \Drewlabs\Packages\Identity\RoleUser())->getEntity(),
            "user_id",
            "role_id",
            "user_id",
            "id"
        )
            ->using(\Drewlabs\Packages\Identity\RoleUser::class)
            ->withPivot([]);
    }



    /**
     * [[user_name]] attribute getter
     *
     * @return string
     */
    public function getUsernameAttribute()
    {
        return isset($this->attributes['user_name']) ? $this->attributes['user_name'] : null;
    }


    /**
     * [[is_verified]] attribute getter
     *
     * @return void
     */
    public function getIsVerifiedAttribute()
    {
        return isset($this->attributes['is_verified']) ? boolval($this->attributes['is_verified']) : true;
    }

    /**
     * Defines the relation between the user model and the user_info model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user_info()
    {
        if (!\is_null(\config('drewlabs_identity.models.user_info.class', null))) {
            return $this->hasOne(\config('drewlabs_identity.models.user_info.class'), 'user_id');
        }
        return $this->hasOne(UserInfo::class, 'user_id');
    }

    /**
     * Return the related user_notification_channels entries
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_channels()
    {
        $this->hasMany(\Drewlabs\Packages\Identity\UserNotificationChannel::class, 'user_id', 'user_id');
    }

    /**
     * Returns the related notification channels associated with the current user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function channels()
    {
        return $this->belongsToMany(
            \Drewlabs\Packages\Identity\NotificationChannel::class,
            "user_notification_channels",
            "user_id",
            "channel_id",
            "user_id",
            "id"
        )
            ->using(\Drewlabs\Packages\Identity\UserNotificationChannel::class)
            ->withPivot([
                'identifier',
                'secret',
            ]);
    }


    /**
     * @inheritDoc
     */
    public function getRelations()
    {
        return \config('drewlabs_identity.models.user.relations', [
            "user_roles.role.permission_roles.permission",
            "user_info.organisation",
            "user_info.agence",
            "user_info.department_user.department",
            "channels"
        ]);
    }

    /**
     * Get the user unique identifier used for connexion processes
     *
     * @return string
     */
    public static function getUserUniqueIdentifier()
    {
        return "user_name";
    }

    /**
     * Get the lock enable status of the user account
     *
     * @return string
     */
    public function getLockEnabled()
    {
        return "lock_enabled";
    }

    /**
     * Get the lock expiration date time
     *
     * @return string
     */
    public function getLockExpireAt()
    {
        return "lock_expired_at";
    }

    /**
     * Get the lock expiration date time
     *
     * @return string
     */
    public function getLoginAttempts()
    {
        return "login_attempts";
    }

    /**
     * @inheritDoc
     */
    public function fromAuthenticatable(Authenticatable $authenticatable)
    {
        // Get the connected user unique identifier
        $authIdentifier = $authenticatable->authIdentifier();
        if (is_null($authIdentifier)) {
            return new static;
        }
        return $this->newQuery()->where(array(array($this->getPrimaryKey(), $authIdentifier)))->first();
    }

    /**
     * @inheritDoc
     */
    public function toAuthenticatable(bool $loadRelated = true)
    {
        $serializer = app(\Drewlabs\Contracts\Auth\IAuthenticatableSerializer::class);
        return $serializer->deserialize($this, $loadRelated);
    }
}
