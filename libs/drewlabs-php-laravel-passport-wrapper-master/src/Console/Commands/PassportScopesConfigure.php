<?php

namespace Drewlabs\Packages\PassportPHPLeagueOAuth\Console\Commands;

use Illuminate\Console\Command;


class PassportScopesConfigure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drewlabs:oauth-scopes-install';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert/Create default scopes in the scopes table';
    /**
     * Execute the console command.
     *
     * @param  \Drewlabs\Packages\PassportPHPLeagueOAuth\Scope  $clients
     * @return void
     */
    public function handle(\Drewlabs\Packages\PassportPHPLeagueOAuth\Scope $scope)
    {
        $scope->insert([
            [
                'label' =>  \Drewlabs\Packages\Identity\DefaultScopes::MANAGE_PASSWORD_SCOPE,
                'description_fr' => 'Authorization de mise à jour de mot de passe d\'utilisateur',
                'description_en' => 'User\'s password update scope',
            ]
        ]);
        $this->info('Password scope created successfully');
    }
}
