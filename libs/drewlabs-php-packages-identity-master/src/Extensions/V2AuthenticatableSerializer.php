<?php

namespace Drewlabs\Packages\Identity\Extensions;

use Drewlabs\Packages\Identity\Traits\AuthenticatableSerializer as AuthenticatableSerializerTrait;
use Drewlabs\Contracts\Auth\IDrewlabsAuthorizable as AuthorizableContract;

class V2AuthenticatableSerializer implements \Drewlabs\Contracts\Auth\IAuthenticatableSerializer
{
    use AuthenticatableSerializerTrait;

    /**
     * {@inheritDoc}
     */
    public function serialize($user)
    {
        $tags = array();
        foreach ($user->{'getFillables'}() as $k) {
            if (in_array($k, $user->{'getGuarded'}())) {
                continue;
            }
            if (property_exists($user, $k)) {
                if (($user instanceof AuthorizableContract) && ($k === $user->permissionPropertyName())) {
                    $permissions = collect($user->{$user->permissionPropertyName()})->map(function ($p) {
                        return $p->label;
                    })->all();
                    $tags[$user->permissionPropertyName()] = $permissions;
                } else if (($user instanceof AuthorizableContract) && ($k === $user->permissionGroupPropertyName())) {
                    $permissions = collect($user->{$user->permissionGroupPropertyName()})->map(function ($p) {
                        return $p->label;
                    })->all();
                    $tags[$user->permissionGroupPropertyName()] = $permissions;
                } else {
                    $tags[$k] = $user->{$k};
                }
            }
        }
        return $tags;
    }
}
