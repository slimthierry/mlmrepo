<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Packages\Identity\Contracts\IUserManager;
use Drewlabs\Contracts\Auth\IAuthenticatableProvider;
use Drewlabs\Contracts\Auth\IUserModel;
use Drewlabs\Packages\Identity\Repository\UserRepository;
use Drewlabs\Contracts\Hasher\IHasher;
use Drewlabs\Packages\Database\Contracts\TransactionUtils;
use Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository;
use Drewlabs\Packages\Identity\Services\Contracts\IDepartmentUsersDataProvider;
use Drewlabs\Packages\Identity\Services\Contracts\IDepartmentsDataProvider;
use Drewlabs\Packages\Identity\Services\Contracts\IUserInfosDataProvider;

class UserManager implements IUserManager
{
    /**
     * Authenticatable provider instance
     *
     * @var IAuthenticatableProvider
     */
    private $provider;

    /**
     * User repository instance provider
     *
     * @param UserRepository $repository
     */
    private $repository;

    /**
     * Undocumented variable
     *
     * @var IHasher
     */
    private $hasher;

    /**
     * Application identity user model
     *
     * @var IUserModel
     */
    private $model;

    /**
     * @var IDepartmentUsersDataProvider
     */
    private $departmentUserDataProvider;

    /**
     * @var IDepartmentsDataProvider
     */
    private $departmentsDataProvider;

    /**
     * @var IUserInfosDataProvider
     */
    private $userInfosDataProvider;

    public function __construct(
        IAuthenticatableProvider $provider,
        BaseIlluminateModelRepository $r,
        IHasher $hasher,
        IUserModel $model
    ) {
        $this->provider = $provider;
        $this->repository = (clone $r)->setModel(IUserModel::class);
        $this->hasher = $hasher;
        $this->model = $model;
        $this->departmentUserDataProvider = app(IDepartmentUsersDataProvider::class);
        $this->departmentsDataProvider = app(IDepartmentsDataProvider::class);
        $this->userInfosDataProvider = app(IUserInfosDataProvider::class);
    }

    /**
     * @inheritDoc
     */
    public function createUser(array $userModelEntries, array $configurationEntries)
    {
        try {
            app(TransactionUtils::class)->startTransaction();
            $user = $this->repository->insert($userModelEntries);
            // Save user roles
            if (isset($configurationEntries['roles'])) {
                $user->user_roles()->createMany(
                    array_map(function ($id) use ($user) {
                        $now = date("Y-m-d H:i:s");
                        return array(
                            "role_id" => $id,
                            "user_id" => $user->getKey(),
                            "created_at" => $now,
                            "updated_at" => $now
                        );
                    }, $configurationEntries['roles'])
                );
            }
            $configurationEntries = array_merge($this->rebuildUserConfigurationEntries($configurationEntries), [
                'user_id' => $user->getKey()
            ]);
            $userInfo = $this->userInfosDataProvider->create($configurationEntries);
            // Verify if user information is successfully created and request input contains "department_id" and if is_department_manager is true
            if (
                isset($userInfo) &&
                isset($configurationEntries['department_id'])
            ) {
                if (\drewlabs_identity_configs('models.department_user.allow_multiple_manager', false) && isset($configurationEntries['is_department_manager']) && boolval($configurationEntries['is_department_manager']) === true) {
                    // Mofify the is_manager state of all user_department relation to false if the current user is a manager and application
                    // does not support more than one manager
                    $this->departmentUserDataProvider->modify([
                        'where' => [
                            ['department_id', $configurationEntries['department_id']]
                        ]
                    ], ['is_manager' => false]);
                }
                // Create a new user department entry for the current user
                $this->departmentUserDataProvider->create([
                    'department_id' => $configurationEntries['department_id'],
                    'agence_id' => $configurationEntries['agence_id'],
                    'is_manager' => isset($configurationEntries['is_department_manager']) ? boolval($configurationEntries['is_department_manager']) : false,
                    'user_id' => $userInfo->getKey()
                ]);
            }
            app(TransactionUtils::class)->completeTransaction();
            return $user;
        } catch (\Exception $e) {
            // Cancel database transaction if any error
            app(TransactionUtils::class)->cancel();
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function updateUser($userId, array $entries, array $configurationEntries = null)
    {
        try {
            app(TransactionUtils::class)->startTransaction();
            $user = $this->repository->findById($userId);
            if (isset($user)) {
                if (isset($entries["password"])) {
                    $user->user_password = $this->hasher->make($entries["password"]);
                }
                if (isset($entries["username"])) {
                    $user->user_name = $entries["username"];
                }
                if (isset($entries["is_active"])) {
                    $user->is_active = $entries["is_active"];
                }
                if (isset($entries["double_auth_active"])) {
                    $user->double_auth_active = $entries["double_auth_active"];
                }
                $user->save();
                // Set the new roles if the user department has changed and update organisation_id in the entries
                if (isset($entries['roles'])) {
                    app(RoleUser::class)->where('user_id', $user->getKey())->delete();
                    $user->user_roles()->createMany(
                        array_map(function ($id) use ($user) {
                            return array(
                                "role_id" => $id,
                                "user_id" => $user->getKey(),
                                "created_at" => date("Y-m-d H:i:s"),
                                "updated_at" => date("Y-m-d H:i:s")
                            );
                        }, $entries['roles'])
                    );
                }
                $entries = $this->rebuildUserConfigurationEntries($entries);
                $this->userInfosDataProvider->modify($user->user_info->getKey(), $entries);
                $user = $this->repository->findById($userId);
                // Set the user as new manager if the manager key is set
                if (
                    isset($user->user_info->department_id) &&
                    isset($entries['is_department_manager'])
                ) {
                    if (\drewlabs_identity_configs('models.department_user.allow_multiple_manager', false) && boolval($configurationEntries['is_department_manager']) === true) {
                        // Mofify the is_manager state of all user_department relation to false if the current user is a manager and application
                        // does not support more than one manager
                        $this->departmentUserDataProvider->modify([
                            'where' => [
                                ['department_id', $configurationEntries['department_id']]
                            ]
                        ], ['is_manager' => false]);
                        // Update or create user_department entry
                        $this->departmentUserDataProvider->create(
                            [
                                'department_id' => $entries['department_id'],
                                'is_manager' => (boolval($entries['is_department_manager'])),
                                'user_id' => $user->user_info->getKey()
                            ],
                            [
                                'method' => 'insert',
                                'upser' => true,
                                'upsert_conditions' => [
                                    'department_id' => $configurationEntries['department_id'],
                                    'user_id' => $user->user_info->getKey()
                                ]
                            ]
                        );
                    }
                }
                app(TransactionUtils::class)->completeTransaction();
                return $user;
            }
            app(TransactionUtils::class)->completeTransaction();
            return $user;
        } catch (\Exception $e) {
            // Cancel database transaction if any error
            app(TransactionUtils::class)->cancel();
            throw new \RuntimeException($e->getMessage());
        }
    }

    private function rebuildUserConfigurationEntries(array $entries)
    {
        if (isset($entries['department_id'])) {
            $department = $this->departmentsDataProvider->getById($entries['department_id']);
            if (isset($department)) {
                $entries["organisation_name"] = $department->company->name;
            }
        }
        return $entries;
    }
    /**
     * @inheritDoc
     */
    public function findUserByRemenberToken($id, $token)
    {
        return $user = $this->provider->findByToken($id, $token);
    }

    /**
     * @inheritDoc
     */
    public function findUserByCredentials(array $credentials)
    {
        return $this->provider->findByCrendentials($credentials);
    }

    /**
     * @inheritDoc
     */
    public function getUserRepository()
    {
        return $this->repository;
    }

    /**
     * @inheritDoc
     */
    public function getIdentityUserModel()
    {
        return $this->model;
    }
}
