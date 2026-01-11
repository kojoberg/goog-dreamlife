<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Branch;

class DowngradeToSingleBranch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pharmacy:downgrade-to-single {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Downgrade the pharmacy from Multi-Branch to Single Location mode (soft downgrade)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏥 Starting Pharmacy Mode Downgrade...');
        $this->newLine();

        // Check if already in single mode
        if (config('pharmacy.mode') === 'single') {
            $this->warn('⚠️  Already in Single Location mode.');
            return Command::SUCCESS;
        }

        // Check for multiple branches
        try {
            $branchCount = Branch::count();
            if ($branchCount > 1) {
                $this->warn("⚠️  You have {$branchCount} branches configured.");
                $this->warn('   After downgrade, only the Main Branch will be visible.');
                $this->warn('   Data from other branches will be preserved but hidden.');
                $this->newLine();

                if (!$this->option('force')) {
                    if (!$this->confirm('Do you want to proceed with the downgrade?')) {
                        $this->info('Downgrade cancelled.');
                        return Command::FAILURE;
                    }
                }
            }
        } catch (\Exception $e) {
            // Branch table might not exist, continue anyway
        }

        // 1. Update .env file
        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $envContents = File::get($envPath);

            if (str_contains($envContents, 'PHARMACY_MODE=multi')) {
                $envContents = str_replace('PHARMACY_MODE=multi', 'PHARMACY_MODE=single', $envContents);
                File::put($envPath, $envContents);
                $this->info('✅ Updated .env: PHARMACY_MODE=single');
            } else {
                // Add the line if it doesn't exist
                $envContents .= "\nPHARMACY_MODE=single\n";
                File::put($envPath, $envContents);
                $this->info('✅ Added PHARMACY_MODE=single to .env');
            }
        } else {
            $this->error('❌ .env file not found!');
            return Command::FAILURE;
        }

        // 2. Clear config cache
        $this->call('config:clear');
        $this->info('✅ Config cache cleared');

        $this->newLine();
        $this->info('🎉 Downgrade Complete!');
        $this->info('   The system is now in Single Location mode.');
        $this->info('   To upgrade back, run: php artisan pharmacy:upgrade-to-multi');
        $this->newLine();

        return Command::SUCCESS;
    }
}
