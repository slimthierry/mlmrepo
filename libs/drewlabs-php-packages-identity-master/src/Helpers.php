<?php

namespace Drewlabs\Packages\Identity;


class Helpers
{

    /**
     * Build the authenticated user key associated with the specified package model
     *
     * @param \Illuminate\Http\Request $request
     * @param string $packageConfigKey
     * @return int|string
     */
    public static function getRequestUserId(\Illuminate\Http\Request $request, $packageConfigKey = null)
    {
        if ($request && !is_null($request->user())) {
            $obj = app(is_null($packageConfigKey) ? UserInfo::class : config($packageConfigKey, UserInfo::class));
            return $obj->fromAuthenticatable($request->user())->getKey();
        }
    }

    /**
     * Get migrated user id key from the authenticatable object
     *
     * @param \Drewlabs\Contracts\Auth\Authenticatable $user
     * @param [type] $packageConfigKey
     * @return int|string
     */
    public static function getUserIdFromAuthenticatable(\Drewlabs\Contracts\Auth\Authenticatable $user, $packageConfigKey = null)
    {
        if (isset($user)) {
            $obj = app(is_null($packageConfigKey) ? UserInfo::class : config($packageConfigKey, UserInfo::class));
            return $obj->fromAuthenticatable($user)->getKey();
        }
    }


    /**
     * Build the authenticated user key associated with the specified package model based on the provided user_id
     *
     * @param string|int $id
     * @param string $packageConfigKey
     * @param string $userIdKeyName
     * @return int|string
     */
    public static function getPackageUserModelIdFromAuthenticatableId($id, $packageConfigKey = null, $userIdKeyName =  'user_id')
    {
        if (!is_null($id)) {
            $obj = app(is_null($packageConfigKey) ? UserInfo::class : config($packageConfigKey, UserInfo::class));
            // Get the user_info where the user_id equals provided id
            $user = $obj->where(array(array($userIdKeyName, $id)))->first();
            return isset($user) ? $user->getKey() : null;
        }
    }

    /**
     * Build the authenticated user name associated with the id
     *
     * @param int|string $id
     * @param string $packageConfigKey
     * @param string $primaryKeyName
     * @return string
     */
    public static function getUsernameFromPackageUserModelId($id, $packageConfigKey = null, $primaryKeyName = 'id')
    {
        if (!is_null($id)) {
            $obj = app(is_null($packageConfigKey) ? UserInfo::class : config($packageConfigKey, UserInfo::class));
            $user = $obj->where(array(array($primaryKeyName, $id)))->first();
            return isset($user) ? $user->getUserDescriptiveNameAttribute() : '';
        }
    }

    /**
     * Build the authenticated user name associated with the request
     *
     * @param \Illuminate\Http\Request $request
     * @param string $packageConfigKey
     * @return string
     */
    public static function getRequestUsernameFromAuthenticatable(\Illuminate\Http\Request $request, $packageConfigKey = null)
    {
        if ($request && !is_null($request->user())) {
            $obj = app(is_null($packageConfigKey) ? UserInfo::class : config($packageConfigKey, UserInfo::class));
            $user = $obj->fromAuthenticatable($request->user());
            return isset($user) ? $user->getUserDescriptiveNameAttribute() : '';
        }
    }

    /**
     * Get the user info from package data configuration
     *
     * @param \Illuminate\Http\Request $request
     * @param string $packageConfigKey
     * @return UserInfo
     */
    public static function getPackageUserFromRequest(\Illuminate\Http\Request $request, $packageConfigKey = null)
    {
        if ($request && !is_null($request->user())) {
            $obj = app(is_null($packageConfigKey) ? UserInfo::class : config($packageConfigKey, UserInfo::class));
            $user = $obj->fromAuthenticatable($request->user());
            return $user;
        }
    }
}
