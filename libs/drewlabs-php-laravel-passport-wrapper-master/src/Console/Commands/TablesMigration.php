<?php

namespace Drewlabs\Packages\PassportPHPLeagueOAuth\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TablesMigration extends Command
{
    /**
     * The full path to the root of the application
     *
     * @var string
     */
    private $app_base_path;

    public function __construct()
    {
        parent::__construct();
        $this->app_base_path = app()->basePath();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drewlabspassport:oauth:migrate';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move migration tables to the application migrations folder';
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Write functionnalities for performing migration
        $local_migrations_path = __DIR__ . '/../../database/migrations';
        $app_migrations_path = $this->app_base_path . DIRECTORY_SEPARATOR . 'database/migrations';
        if (\is_dir($local_migrations_path)) {
            foreach (scandir($local_migrations_path) as $file) {
                # code...
                if ('.' === $file) {
                    continue;
                }
                if ('..' === $file) {
                    continue;
                }
                if (endsWith($file, '.php') && Str::contains($file, 'create')) {
                    $file_path = $local_migrations_path . DIRECTORY_SEPARATOR . $file;
                    $dest_file_path = $app_migrations_path . DIRECTORY_SEPARATOR . $file;
                    if (file_exists($file_path) && !copy($file_path, $dest_file_path)) {
                        echo "Failed copying migration file $file\n";
                    }
                }
            }
        }
    }
}
