<?php


/*
|--------------------------------------------------------------------------
| Identity libraries configurations definitions
|--------------------------------------------------------------------------
|
| The configurations below are related to Identity and
| and authentication libraries requirements
|
*/
return [

    'models' => [
        // Model used for account verification
        "account_verification" => [
            "class" => \Drewlabs\Packages\Identity\AccountVerification::class,
        ],

        // Instance class to be used as the authenicatable object
        "authenticatable" => [
            "class" => \Drewlabs\Packages\Identity\Extensions\IlluminateAuthenticatable::class,
            "fillables" => [
                'id',
                'username',
                'password',
                'is_verified',
                'is_active',
                'double_auth_active',
                'remember_token',
                'roles',
                'permissions',
                'channels',
                'user_info',
            ],
            'serializer' => \Drewlabs\Packages\Identity\Extensions\DefaultAuthenticatableSerializer::class
        ],
        'user_info' => [
            'class' => \Drewlabs\Packages\Identity\UserInfo::class,
            'relations' => [
                'department',
                "organisation",
                "agence",
            ],
            'primaryKey' => 'id',
            'foreign_key' => 'user_id',
            'organisationForeignKey' => 'organisation_name',
            'hidden' => [
                'organisation',
                'department_id',
                'organisation_id',
                'user_id',
                'user_workspaces',
                'email',
                'other_email',
            ],
            'appends' => [
                'company',
                'division',
                'workspaces',
                'emails'
            ]
        ],
        'role' => [
            'class' => \Drewlabs\Packages\Identity\Role::class,
            'foreign_key' => 'role_id',
            'primaryKey' => 'id'
        ],
        'role_user' => [
            'class' => \Drewlabs\Packages\Identity\RoleUser::class,
            'foreign_key' => \Drewlabs\Packages\Identity\RoleUser::getUserIdFieldName()
        ],
        'permissions' => [
            'class' => \Drewlabs\Packages\Identity\Permission::class,
            'primaryKey' => 'id'
        ],
        'permission_roles' => [
            'class' => \Drewlabs\Packages\Identity\PermissionRole::class,
            'primaryKey' => 'id'
        ],
        'user' => [
            'class' => \Drewlabs\Packages\Identity\User::class,
            'relations' => [
                "user_roles.role.permission_roles.permission",
                "roles.permissions",
                "user_info.organisation",
                "user_info.agence",
                "user_info.department_user.department",
                "channels",
            ],
            'table' => 'users',
            'primaryKey' => 'user_id'
        ],
        'agence' => [
            'class' => \Drewlabs\Packages\Identity\Agence::class,
            'foreign_key' => 'agence_id',
            'primaryKey' => 'id',
            'table' => 'agences'
        ],
        'department' => [
            'class' => \Drewlabs\Packages\Identity\Department::class,
            'foreign_key' => 'department_id',
            'primaryKey' => 'id',
            'table'  => 'departments'
        ],
        'department_user' => [
            'class' => \Drewlabs\Packages\Identity\DepartmentUser::class,
            'foreign_key' => 'department_user_id',
            'primaryKey' => 'id',
            'user_column_name' => 'manager_id',
            "allow_multiple_manager" => env('ALLOW_MULTIPLE_MANAGER', false)
        ],
        'organisation' => [
            'class' => \Drewlabs\Packages\Identity\Organisation::class,
            'foreign_key' => 'organisation_name',
            'primaryKey' => 'id',
            'table'  => 'organisations',
            'labelKey' => 'name'
        ],
        'workspace' => [
            'class' => \Drewlabs\Packages\Workspace\Models\Workspace::class,
            'primaryKey' => 'id'
        ],
        'user_workspace' => [
            'class' => \Drewlabs\Packages\Workspace\Models\UserWorkspace::class,
            'primaryKey' => 'id',
            'userForeignKey' => 'user_id',
            'workspaceForeignKey' => 'workspace_id',
            'table' => 'user_workspaces',
            'pivot' => [
                'user_group_id',
                'status',
                'notification_enabled',
                'dashboard_module_id'
            ]
        ],
        'mails' => [
            'class' => \Drewlabs\Packages\Notification\Models\Mail::class,
        ],
        'sms' => [
            'class' => \Drewlabs\Packages\Notification\Models\Sms::class
        ]
    ],
    'requests' => [
        'create_user' => [
            'rules' => [
                'class' => \Drewlabs\Packages\Identity\Http\Requests\UserRequest::class
            ]
        ]
    ],
    'workspace' => [
        'user_workspace' => [
            'service' => \Drewlabs\Packages\Workspace\Services\UserWorkspaceManager::class,
        ]
    ],
    'notify_on_create' => env('NOTIFY_ON_CREATE', false),
    'has_workspace' => env('HAS_WORKSPACE_PACKAGE', false),
    'runs_organisation_entities_migrations' => env('RUN_ORGANISATION_ENTITIES_MIGRATIONS', false),
    'login_attempts' => env('MAX_LOGIN_ATTEMPTS', 5),

    #region Admin user and groups definitions
    'admin_group' => env('SYSADMIN_ROLE', \Drewlabs\Packages\Identity\IdentityDefaultPermissionGroups::SUPER_ADMIN_GROUP),
    'admin_username' => \env('APP_ADMIN_USERNAME'),
    'admin_firstname' => \env('APP_SYSADMIN_FIRSTNAME'),
    'admin_lastname' => \env('APP_ADMIN_LASTNAME'),
    'admin_email' => \env('APP_ADMIN_EMAIL'),
    'admin_password' => \env('APP_SYSADMIN_PASSWORD'),
    #endregion

    #region Definition of the policy middleware key
    'policy_middleware' => 'policy',
    'all_authorization' => \Drewlabs\Packages\Identity\DefaultScopes::SUPER_ADMIN_SCOPE,
    #endregion

    #region Default user role id
    'default_user_role' => env('DEFAULT_USER_ROLE_ID', 31),
    #endregion

    # Apply user filter configuration value
    'apply_users_filter' => env('APPLY_USER_FILTERS', false)
];
