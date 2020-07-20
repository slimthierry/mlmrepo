<?php //

namespace Drewlabs\Packages\Identity\Console\Commands;

use Illuminate\Console\Command;

class CreateAppAuthorization extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drewlabs:identity:create-authorizations {--label= : Authorization unique label} {--displayLabel= : Authorization description texte} {--parfile= : Path to parameters file}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create if not exists a system privilege';
    /**
     * Execute the console command.
     *
     * @param \Drewlabs\Core\Validator\Contracts\IValidator $validator
     * @return void
     */
    public function handle(
        \Drewlabs\Core\Validator\Contracts\IValidator $validator
    ) {
        $parfilePath = $this->option('parfile');
        if (isset($parfilePath)) {
            if (file_exists($parfilePath) && \Drewlabs\Utils\Str::endsWith(basename($parfilePath), '.json')) {
                $contents = file_get_contents($parfilePath);
                $jsonDecoded = json_decode($contents, true);
                if ($jsonDecoded) {
                    $permissions = array_map(function ($p) use ($validator) {
                        return $this->insertAuthorization($validator, $p);
                    }, $jsonDecoded);
                    $this->printPermissionsAsTable(
                        array_values(
                            array_map(
                                function ($p) {
                                    return [
                                        'id' => $p->getKey(),
                                        'label' => $p->label,
                                        'display_label' => $p->display_label
                                    ];
                                },
                                array_filter($permissions, function ($dirtyPermission) {
                                    return isset($dirtyPermission);
                                })
                            )
                        )
                    );
                }
            }
            return;
        }
        $label = $this->option('label');
        $displayLabel = $this->option('displayLabel');
        $permission = $this->insertAuthorization($validator, [
            'label' => $label,
            'display_label' => $displayLabel
        ]);
        $this->printAuthorization($permission);
    }

    public function insertAuthorization(\Drewlabs\Core\Validator\Contracts\IValidator $validator, $values)
    {
        $validator = $validator->validate(array(
            "label" => 'required|max:45|min:3',
            "display_label" => "required|max:255|min:5"
        ), $values);
        if ($validator->fails()) {
            $this->error('Create authorization process fails with validation errors. Make sure you provide --label, --displayLabel or --parfile options');
            return;
        }
        $permission = app(\drewlabs_identity_configs(
            'models.permissions.class',
            \Drewlabs\Packages\Identity\Permission::class
        ))->firstOrCreate(['label' => $values['label']], $values);
        return $permission;
    }

    private function printAuthorization($permission)
    {
        if (!is_null($permission)) {
            $this->info('Privilege successfully created');
            $this->info('Privilege ID: ' . $permission->getKey());
            $this->info('Label: ' . $permission->label);
            $this->info('Display label : ' . $permission->display_label);
        }
    }

    private function printPermissionsAsTable(array $permissions)
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
