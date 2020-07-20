<?php

namespace Drewlabs\Packages\PassportPHPLeagueOAuth\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Passport\ClientRepository;
use DateTime;
use Laravel\Passport\Passport;


class Purge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passport:purge';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired refresh tokens and their associated tokens from the database';
    /**
     * Execute the console command.
     *
     * @param  \Laravel\Passport\ClientRepository  $clients
     * @return void
     */
    public function handle(ClientRepository $clients)
    {
        $db = get_db_connection();
        $count = $db->connection()->table('oauth_refresh_tokens')->where('expires_at', '<', new DateTime())->delete();
        if (Passport::$refreshTokensExpireAt && Passport::$tokensExpireAt) {
            $difference = Passport::$refreshTokensExpireAt->getTimestamp() - Passport::$tokensExpireAt->getTimestamp();
            // We assume it's safe to delete tokens that cannot be refreshed anyway
            $count += $db->connection()->table('oauth_access_tokens')
                ->where('expires_at', '<', (new DateTime())->setTimestamp(time() - $difference))
                ->delete();
        }
        $this->info('Successfully deleted expired tokens: ' . $count);
    }
}
