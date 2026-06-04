<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class SiteUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:up';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bring the application out of maintenance mode';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Setting::updateOrCreate(
            ['key' => 'maintenance_mode'],
            ['value' => '0']
        );

        $this->info('Application is now live.');
        return 0;
    }
}
