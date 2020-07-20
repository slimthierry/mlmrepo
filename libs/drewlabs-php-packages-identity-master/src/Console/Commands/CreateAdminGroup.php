<?php

namespace Drewlabs\Packages\Identity\Console\Commands;

use Illuminate\Console\Command;

class CreateAdminGroup extends Command
{

    /**
     *
     * @var \Drewlabs\Packages\Database\DataTransactionUtils
     */
    private $transaction;

    public function __construct(\Drewlabs\Packages\Database\DataTransactionUtils $transaction)
    {
        parent::__construct();
        $this->transaction = $transaction;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drewlabs:identity:create-app-admin-group';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert/Create the application system administrator group';
    /**
     * Execute the console command.
     *
     * @param \Drewlabs\Core\Validator\Contracts\IValidator $validator
     * @return void
     */
    public function handle()
    {
        try {
            $this->insertOrFindAdminGroup();
            $this->info("Admin group successfully created");
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Insert or find app sys admin group
     *
     * @return \Drewlabs\Contracts\Data\IModelable
     */
    public function insertOrFindAdminGroup()
    {
        $role = [
            'roles_label' => \drewlabs_identity_configs("admin_group"),
            'roles_display_name' => 'SYSTEM ADMINISTRATOR',
            'roles_is_admin_user_role' => true,
            'permissions' => [
                [
                    'permissions_label' => 'all',
                    'permissions_display_name' => 'ALL PRIVILEGES',
                ],
                [
                    'permissions_label' => 'list-permissions',
                    'permissions_display_name' => 'LIST PERMISSIONS PRIVILEGE',
                ],
                [
                    'permissions_label' => 'create-permissions',
                    'permissions_display_name' => 'CREATE PERMISSIONS PRIVILEGE',
                ],
                [
                    'permissions_label' => 'update-permissions',
                    'permissions_display_name' => 'UPDATE PERMISSIONS PRIVILEGE',
                ],
                [
                    'permissions_label' => 'delete-permissions',
                    'permissions_display_name' => 'DELETE PERMISSIONS PRIVILEGE',
                ],
                [
                    'permissions_label' => 'list-roles',
                    'permissions_display_name' => 'LIST ROLES PRIVILEGE',
                ],
                [
                    'permissions_label' => 'create-roles',
                    'permissions_display_name' => 'CREATE ROLES PRIVILEGE',
                ],
                [
                    'permissions_label' => 'update-roles',
                    'permissions_display_name' => 'UPDATE ROLES PRIVILEGE',
                ],
                [
                    'permissions_label' => 'delete-roles',
                    'permissions_display_name' => 'DELETE ROLES PRIVILEGE',
                ],
                [
                    'permissions_label' => 'list-users',
                    'permissions_display_name' => 'LIST USERS PRIVILEGE',
                ],
                [
                    'permissions_label' => 'create-users',
                    'permissions_display_name' => 'CREATE USERS PRIVILEGE',
                ],
                [
                    'permissions_label' => 'update-users',
                    'permissions_display_name' => 'UPDATE USERS PRIVILEGE',
                ],
                [
                    'permissions_label' => 'delete-users',
                    'permissions_display_name' => 'DELETE USERS PRIVILEGE',
                ],
            ],
        ];
        try {
            $this->transaction->startTransaction();
            $created_role = app(\drewlabs_identity_configs('models.role.class'))->firstOrCreate(
                ['label' => $role['roles_label']],
                [
                    'label' => $role['roles_label'],
                    'display_label' => $role['roles_display_name'],
                    'is_admin_user_role' => isset($role['roles_is_admin_user_role']) ? $role['roles_is_admin_user_role'] : false,
                ]
            );
            $permissions = $role['permissions'];
            if (isset($permissions)) {
                foreach ($permissions as $p) {
                    $permission = app(\drewlabs_identity_configs('models.permissions.class', \Drewlabs\Packages\Identity\Permission::class))->firstOrCreate(
                        ['label' => $p['permissions_label']],
                        [
                            'label' => $p['permissions_label'],
                            'display_label' => $p['permissions_display_name'],
                        ]
                    );
                    // Add role Permission table
                    app(\drewlabs_identity_configs('models.permission_roles.class', \Drewlabs\Packages\Identity\PermissionRole::class))->firstOrCreate(
                        [
                            'role_id' => $created_role->id,
                            'permission_id' => $permission->id,
                        ]
                    );
                }
            }
            $this->transaction->completeTransaction();
            return $created_role;
        } catch (\Exception $e) {
            $this->transaction->cancel();
            throw new \RuntimeException($e->getMessage());
        }
    }
}
