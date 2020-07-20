<?php

namespace Drewlabs\Packages\Identity\Extensions;

use Drewlabs\Contracts\Auth\IAuthenticatablePolicy;
use Drewlabs\Contracts\Auth\Authenticatable as IAuthenticatable;
use Drewlabs\Core\Auth\Policies\PolicyClientProvider;
use Drewlabs\Contracts\Auth\IUserModel;

class IdentityPolicyProvider extends PolicyClientProvider implements IAuthenticatablePolicy
{

    /**
     * @inheritDoc
     */
    public function hasAdminRole(IAuthenticatable $authenticatable)
    {
        $user = app(IUserModel::class)->fromAuthenticatable($authenticatable);
        if (is_null($user)) {
            return false;
        }
        $hasAdminRole = false;
        foreach ($user->user_roles as $user_role) {
            # code...
            if (($user_role->role->is_admin_user_role === true) || ($user_role->role->is_admin_user_role === 1)) {
                $hasAdminRole = true;
            break;
            }
        }
        return $hasAdminRole;
    }
}

