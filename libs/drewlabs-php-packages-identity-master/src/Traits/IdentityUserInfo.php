<?php

namespace Drewlabs\Packages\Identity\Traits;

trait IdentityUserInfo
{
    /**
     * @inheritDoc
     */
    public function getRelations()
    {
        return \config('drewlabs_identity.models.user_info.relations', [
            'department',
            "organisation",
            "agence"
        ]);
    }

    /**
     * [[company]] attribute getter method definition
     *
     * @return \stdClass|object
     */
    public function getCompanyAttribute()
    {
        return $this->organisation;
    }

    /**
     * [[department]] attribute getter method definition
     *
     * @return \stdClass|object
     */
    public function getDivisionAttribute()
    {
        return $this->department;
    }

    /**
     * Return the list of managers for the current user
     */
    public function getManagersAttribute()
    {
        return \Drewlabs\Packages\Identity\DepartmentUser::where(
            array(
                array('department_id', $this->department_id),
                array('is_manager', true)
            )
        )->get()
            ->filter(function ($departmentUser) {
                return isset($departmentUser) && isset($departmentUser->user);
            })
            ->map(function ($departmentUser) {
                return $departmentUser->user->getUserDescriptiveNameAttribute();
            });
    }



    /**
     * [[is_manager]] attribute getter method definition
     *
     * @return boolean
     */
    public function getIsManagerAttribute()
    {
        return (bool)($this->department_user->is_manager) === true;
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\Drewlabs\Packages\Identity\User::class, 'user_id', 'user_id');
    }

    /**
     * Return a belongsTo relationship between the current model and the organisation model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organisation()
    {
        return $this->belongsTo(
            \config('drewlabs_identity.models.organisation.class', \Drewlabs\Packages\Identity\Organisation::class),
            \config('drewlabs_identity.models.user_info.organisationForeignKey', 'organisation_name'),
            \config('drewlabs_identity.models.organisation.labelKey', 'name')
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department_user()
    {
        return $this->hasOne(\drewlabs_identity_configs('models.department_user.class', \Drewlabs\Packages\Identity\DepartmentUser::class), 'user_id', 'id');
    }

    // /**
    //  * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
    //  */
    // public function department()
    // {
    //     return $this->belongsTo(
    //         \config('drewlabs_identity.models.department.class', \Drewlabs\Packages\Identity\Department::class),
    //         \config('drewlabs_identity.models.department.foreign_key', 'department_id'),
    //         \config('drewlabs_identity.models.department.primaryKey', 'id')
    //     );
    // }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agence()
    {
        return $this->belongsTo(
            \config('drewlabs_identity.models.agence.class', '\\Drewlabs\\Packages\\Identity\\Agency'),
            \config('drewlabs_identity.models.agence.foreign_key', 'agence_id'),
            \config('drewlabs_identity.models.agence.primaryKey', 'id')
        );
    }

    public function user_workspaces()
    {
        $userWorkspaceModel = \config('drewlabs_identity.models.user_workspace.class', \Drewlabs\Packages\Workspace\Models\UserWorkspace::class);
        $pivot = \config('drewlabs_identity.models.user_workspace.pivot', []);
        return $this->belongsToMany(
            \config('drewlabs_identity.models.workspace.class', \Drewlabs\Packages\Workspace\Models\Workspace::class),
            \config('drewlabs_identity.models.user_workspace.table', 'user_workspaces'),
            \config('drewlabs_identity.models.user_workspace.userForeignKey', 'user_id'),
            \config('drewlabs_identity.models.user_workspace.workspaceForeignKey', 'workspace_id'),
            "id",
            \config('drewlabs_identity.models.workspace.primaryKey', 'id')
        )
            ->using($userWorkspaceModel)
            ->withPivot($pivot);
    }

    /**
     * Returns the user name attribute associated with this info
     *
     * @return string|null
     */
    public function getUsernameAttribute()
    {
        $user = $this->user;
        return !(isset($user)) || is_null($user) ? null : $user->getUsernameAttribute();
    }

    /**
     * Returns the workspace attached to the current user
     *
     * @return array
     */
    public function getWorkspacesAttribute()
    {
        if (is_null(\config('drewlabs_identity.has_workspace', null)) || (filter_var(\config('drewlabs_identity.has_workspace'), FILTER_VALIDATE_BOOLEAN) == false)) {
            return [];
        }
        $userWorkspaces = $this->user_workspaces;
        if ($userWorkspaces) {
            return $userWorkspaces->map(function ($item) {
                if ($item->pivot && is_object($item->pivot)) {
                    $userGroup = $item->pivot->workspace_user_group;
                    $authorizations = $userGroup->authorizations->map(function ($authorization) {
                        return $authorization->label;
                    });
                    $workspaceUser = $item->pivot->getAttributes();
                    $item = $item->setWithoutAppends(true)->withoutRelations();
                    $value = array_merge($item->getAttributes(), array(
                        'user_group' => array_merge(
                            $userGroup->getAttributes(),
                            array('authorizations' => $authorizations)
                        ),
                        'workspace_user' => $workspaceUser
                    ));
                    unset($value['workspace_id']);
                    return $value;
                }
                return $item;
            });
        }
        return [];
    }

    /**
     * Returns the list of user emails
     *
     * @return array
     */
    public function getEmailsAttribute()
    {
        return array_filter([$this->email, $this->other_email], function ($item) {
            return !is_null($item);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function fromAuthenticatable(\Drewlabs\Contracts\Auth\Authenticatable $authenticatable)
    {
        // Get the connected user unique identifier
        $authIdentifier = $authenticatable->authIdentifier();
        if (is_null($authIdentifier)) {
            return app(\Drewlabs\Contracts\Auth\IUserModel::class);
        }
        return $this->newQuery()->where(array(array('user_id', $authIdentifier)))->first();
    }

    /**
     * {@inheritDoc}
     */
    public function toAuthenticatable($shouldLoadRelations = false)
    {
        return $this->user->toAuthenticatable($shouldLoadRelations);
    }


    /**
     * [[workspace_authorizations]] attribute setter
     *
     * @return static
     */
    public function setWorkspaceUserGroupAttribute($value)
    {
        $this->attributes['workspace_user'] = $value;
        return $this;
    }

    /**
     * [[user_descriptive_name]] getter method
     *
     * @return string
     */
    public function getUserDescriptiveNameAttribute()
    {
        $fullname = null;
        if (isset($this->attributes['firstname']) && isset($this->attributes['lastname'])) {
            $fullname = $this->attributes['firstname'] . ', ' . $this->attributes['lastname'];
        }
        return isset($fullname) ? $fullname . "(" . $this->user->getUserName() . ")" : $this->user->getUserName();
    }

    /**
     * @inheritDoc
     */
    public function getHidden()
    {
        return \config(
            'drewlabs_identity.models.user_info.hidden',
            array('organisation', 'department_id', 'organisation_id', 'user_id', 'user_workspaces', 'email', 'other_email')
        );
    }

    /**
     * @inheritDoc
     */
    protected function getArrayableAppends()
    {
        $route = $this->getIndexRoute();
        if ($this->withoutAppends) {
            return !is_null($route) && is_string($route) ? array('_link') : array();
        }
        return array_merge(\config(
            'drewlabs_identity.models.user_info.appends',
            array('is_manager', 'company', 'division', 'workspaces', 'emails')
        ), isset($route) && is_string($route) ? array('_link') : array());
    }
}
