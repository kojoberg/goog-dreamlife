<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpgradeToMultiBranch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pharmacy:upgrade-to-multi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade the pharmacy from Single Location to Multi-Branch mode';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏥 Starting Pharmacy Mode Upgrade...');
        $this->newLine();

        // 1. Update .env file
        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $envContents = File::get($envPath);

            if (str_contains($envContents, 'PHARMACY_MODE=single')) {
                $envContents = str_replace('PHARMACY_MODE=single', 'PHARMACY_MODE=multi', $envContents);
                File::put($envPath, $envContents);
                $this->info('✅ Updated .env: PHARMACY_MODE=multi');
            } elseif (str_contains($envContents, 'PHARMACY_MODE=multi')) {
                $this->warn('⚠️  Already in Multi-Branch mode.');
                return Command::SUCCESS;
            } else {
                // Add the line if it doesn't exist
                $envContents .= "\nPHARMACY_MODE=multi\n";
                File::put($envPath, $envContents);
                $this->info('✅ Added PHARMACY_MODE=multi to .env');
            }
        } else {
            $this->error('❌ .env file not found!');
            return Command::FAILURE;
        }

        // 2. Update database settings if available
        try {
            $settings = \App\Models\Settings::first();
            if ($settings) {
                $settings->pharmacy_mode = 'multi';
                $settings->save();
                $this->info('✅ Updated Settings: pharmacy_mode=multi');
            }
        } catch (\Exception $e) {
            $this->warn('⚠️  Could not update database settings: ' . $e->getMessage());
        }

        // 3. Clear config cache
        $this->call('config:clear');
        $this->info('✅ Config cache cleared');

        $this->newLine();
        $this->info('🎉 Upgrade Complete!');
        $this->info('   You can now manage multiple branches from Settings → Multi-Branch Operations');
        $this->newLine();

        return Command::SUCCESS;
    }
}
