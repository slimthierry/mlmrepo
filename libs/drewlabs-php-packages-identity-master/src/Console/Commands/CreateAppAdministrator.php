<?php

namespace Drewlabs\Packages\Identity\Console\Commands;

use Drewlabs\Packages\Identity\Contracts\IUserManager;
use Illuminate\Console\Command;

class CreateAppAdministrator extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drewlabs:identity:create-app-admin {--username= : Administration account username} {--password= : Administration account username} {--firstname= : Administration user account firstname} {--lastname= : Administration user account lastname} {--email= : Administration user account email address}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create if not exists the system administration user account';
    /**
     * Execute the console command.
     *
     * @param \Drewlabs\Core\Validator\Contracts\IValidator $validator
     * @return void
     */
    public function handle(
        \Drewlabs\Core\Validator\Contracts\IValidator $validator,
        \Drewlabs\Contracts\Hasher\IHasher $hash,
        IUserManager $userManager,
        \Drewlabs\Packages\Identity\Console\Commands\CreateAdminGroup $createAdminGroupCommand
    ) {
        #region Load configuration from command options or configuration
        $username = is_null($this->option('username')) ?
            \drewlabs_identity_configs("admin_username") : $this->option('username');
        $password = is_null($this->option('password')) ?
            \drewlabs_identity_configs("admin_password", "Secret") : $this->option('password');
        $firstname = is_null($this->option('firstname')) ?
            \drewlabs_identity_configs("admin_firstname") : $this->option('firstname');
        $lastname = is_null($this->option('lastname')) ?
            \drewlabs_identity_configs("admin_lastname") : $this->option('lastname');
        $email = is_null($this->option('email')) ?
            \drewlabs_identity_configs("admin_email") : $this->option('email');
        #endregion

        #region Create or Find SysAdmin role
        $role = $createAdminGroupCommand->insertOrFindAdminGroup();
        #endregion

        #region Build user details entries and validate
        if (!(\filter_var($username, FILTER_VALIDATE_EMAIL) === false)) {
            // Username value is an email, should generate
            $username = \explode('@', $username)[0] . \Drewlabs\Utils\Rand::int(100, 999);
        }
        $inputs = [
            "username" => $username,
            "password" => $password,
            "password_confirmation" => $password,
            "is_active" => true,
            "is_verified" => true,
            "remember_token" => null,
            "double_auth_active" => false,
            "created_by" => null,
            "roles" => [$role->getKey()],
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
        ];
        $validator = $validator->validate(
            app(\config('drewlabs_identity.requests.create_user.rules.class', \Drewlabs\Packages\Identity\Http\Requests\UserRequest::class)),
            $inputs
        );
        if ($validator->fails()) {
            $this->error('Create user process fails with validation errors. Make sure you provide --username, --password, --firstname, --lastname and --email options or you configure them in the application environment');
            return;
        }
        #endregion
        #region Insert or create admin account
        $userManager->createUser([
            "user_name" => $username,
            "user_password" => $hash->make($password),
            "is_active" => true,
            "is_verified" => true,
            "remember_token" => null,
            "double_auth_active" => false,
            "created_by" => null,
        ], $inputs);
        #endregion
        $this->info("Admin user created successfully");
    }
}
