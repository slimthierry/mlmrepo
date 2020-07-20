<?php

namespace Drewlabs\Packages\Identity\Extensions;

use Drewlabs\Packages\Identity\Contracts\IUserService;
use Drewlabs\Packages\Database\Extensions\CustomQueryCriteria;
use Drewlabs\Packages\Identity\DefaultScopes;
use Drewlabs\Contracts\Auth\IAuthenticatablePolicy;
use Drewlabs\Contracts\Auth\IUserModel;
use Illuminate\Contracts\Auth\Guard;

/**
 * @deprecated 1.0.0
 * This class is replaced by Drewlabs\\Packages\\Identity\\UserManager for compact user management
 * implementation.
 */
class UserService implements IUserService
{


    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function createUserExtraInformation($request, $user_id, $role_label)
    {
        // Create user information if exists in database
        $user = app()[Guard::class]->user();
        $organisation = null;
        $user_info_inputs = array();
        if (app()[IAuthenticatablePolicy::class]->hasPermission($user, DefaultScopes::SUPER_ADMIN_SCOPE)) {
            // Create a CustomQueryCriteria instance
            $query_filters = new CustomQueryCriteria(
                array(
                    'where' => array(array('label', $role_label))
                )
            );
            // Apply the curstom query to the Role repository and retrieve the first item that matches
            $entry = app('Drewlabs\\Packages\\Identity\\Repository\\RoleRepository')->pushFilter($query_filters)->find()->first();
            // If the role entry is found and is not an admin role, create the ressources associated to the user
            if ($entry && ($entry->is_admin_user_role == 0)) {
                $organisation = app()['Drewlabs\\Packages\\Identity\\Repository\\OrganisationRepository']->insert(
                    $request->all(),
                    true
                );
            }
        } else {
            $user = app()[IUserModel::class]->fromAuthenticatable(app()[Guard::class]->user());
            $organisation = $user->user_info->organisation;
            $user_info_inputs['usr_info_parent_id'] = $user->user_info->id;
        }
        // Set the organisation to the owner account organusation if the user is not an admin
        app('Drewlabs\\Packages\\Identity\\Repository\\UserInfoRepository')->insert(array_merge(
            $request->all(),
            array_merge(
                $user_info_inputs,
                array(
                    "usr_info_user_id" => $user_id,
                    "usr_info_organisation_id" => isset($organisation) ? $organisation->{$organisation->getPrimaryKey()} : $organisation
                )
            )
        ), true);
    }

    /**
     * @inheritDoc
     */
    public function updateUserExtraInformation($request, $user_id)
    {
        // Update user informations in the database
        // Update the user organisation informations
        app()
            ->make('Drewlabs\\Packages\\Identity\\Repository\\OrganisationRepository')
            ->pushFilter(
                new CustomQueryCriteria(array('where' => array(array('user_id', $user_id))))
            )
            ->update(
                array_merge($request->all(), array("usr_info_user_id" => null, "usr_info_organisation_id" => null, "usr_info_parent_id" => null)),
                array(),
                true
            );
    }
}
