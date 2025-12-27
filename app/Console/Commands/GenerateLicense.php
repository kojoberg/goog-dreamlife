<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LicenseService;

class GenerateLicense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:generate {months=12 : Duration of the license in months}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new license key for the pharmacy software.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $months = (int) $this->argument('months');
        $service = new LicenseService();
        $key = $service->generateKey($months);

        $this->info("Generated License Key ($months months):");
        $this->line("");
        $this->comment($key);
        $this->line("");
        $this->info("Copy and paste this key into the Settings page.");
    }
}
