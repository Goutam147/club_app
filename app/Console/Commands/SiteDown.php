<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class SiteDown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:down {message? : Custom maintenance message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Put the application into maintenance mode';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $setting = Setting::updateOrCreate(
            ['key' => 'maintenance_mode'],
            ['value' => '1']
        );

        if ($this->argument('message')) {
            Setting::updateOrCreate(
                ['key' => 'maintenance_message'],
                ['value' => $this->argument('message')]
            );
        }

        $this->info('Application is now in maintenance mode.');
        return 0;
    }
}
