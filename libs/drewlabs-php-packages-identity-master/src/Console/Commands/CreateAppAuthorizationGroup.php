<?php

namespace Drewlabs\Packages\Identity\Console\Commands;

use Illuminate\Console\Command;

class CreateAppAuthorizationGroup extends Command
{
    /**
     *
     * @var \Drewlabs\Packages\Identity\Services\Contracts\IRolesDataProvider
     */
    private $provider;

    /**
     *
     * @param \Drewlabs\Packages\Identity\Services\Contracts\IRolesDataProvider $provider
     */
    public function __construct(\Drewlabs\Packages\Identity\Services\Contracts\IRolesDataProvider $provider)
    {
        parent::__construct();
        $this->provider = $provider;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drewlabs:identity:create-authorization-group {--label= : Authorization group unique label} {--displayLabel= : Authorization group description texte} {--authorizations= : Comma separated list of labels}  {--parfile= : Path to parameters file}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new authorization group that can be assigned to a user';
    /**
     * Execute the console command.
     *
     * @param \Drewlabs\Core\Validator\Contracts\IValidator $validator
     * @return void
     */
    public function handle(\Drewlabs\Core\Validator\Contracts\IValidator $validator)
    {
        $parfilePath = $this->option('parfile');
        if (isset($parfilePath)) {
            if (file_exists($parfilePath) && \Drewlabs\Utils\Str::endsWith(basename($parfilePath), '.json')) {
                $contents = file_get_contents($parfilePath);
                $jsonDecoded = json_decode($contents, true);
                if ($jsonDecoded) {
                    $authorizationGroup = array_map(function ($p) use ($validator) {
                        return $this->insertAuthorizationGroup($validator, $p);
                    }, $jsonDecoded);
                    $this->printAuthorizationGroupAs(
                        array_values(
                            array_map(
                                function ($p) {
                                    return ['id' => $p->getKey(), 'label' => $p->label, 'display_label' => $p->display_label];
                                },
                                array_filter($authorizationGroup, function ($group) {
                                    return isset($group);
                                })
                            )
                        )
                    );
                } else {
                    $this->error('The json file provided with the --parfile option contains an invalid json input');
                }
            } else {
                $this->error('Please provide a valid json file path when using --parfile option');
            }
            return;
        }
        // If parfile option is not provided
        $authorizations = $this->option('authorizations');
        if (is_null($authorizations)) {
            $this->error('Please provide a comma seperated list of authorization to the --authorizations option');
            return;
        }
        $label = $this->option('label');
        $displayLabel = $this->option('displayLabel');
        $authorizationGroup = $this->insertAuthorizationGroup($validator, [
            'label' => $label,
            'display_label' => $displayLabel,
            'permissions' => array_map(function ($item) {
                return trim($item);
            }, \Drewlabs\Utils\Str::toArray($authorizations))
        ]);
        $this->printAuthorizationGroup($authorizationGroup);
    }



    public function insertAuthorizationGroup(\Drewlabs\Core\Validator\Contracts\IValidator $validator, $values)
    {
        if (!isset($values['permissions'])) {
            $this->error('Bad input data : Authorizations are required to create a new authorization group.');
            return;
        }
        $values['permissions'] = app(\drewlabs_identity_configs(
            'models.permissions.class',
            \Drewlabs\Packages\Identity\Permission::class
        ))->whereIn('label', $values['permissions'])
            ->get()->map(function ($p) {
                return $p->getKey();
            })->all();
        $validator = $validator->validate(array(
            "label" => 'required|max:45|min:3',
            "display_label" => "required|max:255|min:5",
            "description" => "nullable",
            "permissions" => "sometimes",
            "permissions.*" => "exists:permissions,id"
        ), $values);
        if ($validator->fails()) {
            $this->error('Create authorization group process fails with validation errors. Make sure you provide --label, --displayLabel, --authorizations or --parfile options');
            return;
        }
        $this->provider->delete(['where' => array(array('label', $values['label']))]);
        if (isset($values['permissions'])) {
            $values['permission_roles'] = array_map(function ($id) {
                return array("permission_id" => $id);
            }, $values['permissions']);
        }
        $permission = $this->provider->create($values, new \Drewlabs\Core\Data\DataProviderCreateHandlerParams([
            'method' => 'insert__permission_roles',
            'upsert' => true,
            'upsert_conditions' => [
                'label' => $values['label'],
            ]
        ]));
        return $permission;
    }

    private function printAuthorizationGroup($permission)
    {
        if (!is_null($permission)) {
            $this->info('Privilege successfully created');
            $this->info('Privilege ID: ' . $permission->getKey());
            $this->info('Label: ' . $permission->label);
            $this->info('Display label : ' . $permission->display_label);
        }
    }

    private function printAuthorizationGroupAs(array $permissions)
    {
        $this->info("\nList of added privileges \n");
        $headers = [
            'ID',
            'Label',
            'Display label'
        ];
        $this->table($headers, $permissions);
    }
}
