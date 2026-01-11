<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpgradeToMultiBranch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pharmacy:upgrade-to-multi {--force : Force upgrade even if already in multi mode}';

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

        $force = $this->option('force');

        // 1. Update .env file
        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $envContents = File::get($envPath);

            if (str_contains($envContents, 'PHARMACY_MODE=single')) {
                $envContents = str_replace('PHARMACY_MODE=single', 'PHARMACY_MODE=multi', $envContents);
                File::put($envPath, $envContents);
                $this->info('✅ Updated .env: PHARMACY_MODE=multi');
            } elseif (str_contains($envContents, 'PHARMACY_MODE=multi')) {
                if (!$force) {
                    $this->warn('⚠️  Already in Multi-Branch mode. Use --force to run super admin setup.');
                    return Command::SUCCESS;
                }
                $this->info('ℹ️  Already in Multi-Branch mode, continuing with --force...');
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
            $settings = \App\Models\Setting::first();
            if ($settings) {
                $settings->pharmacy_mode = 'multi';
                $settings->save();
                $this->info('✅ Updated Settings: pharmacy_mode=multi');
            }
        } catch (\Exception $e) {
            $this->warn('⚠️  Could not update database settings: ' . $e->getMessage());
        }

        // 3. Super Admin Setup
        $this->newLine();
        $this->info('👑 Super Admin Setup');
        $this->info('   A Super Admin can manage ALL branches and has unrestricted access.');
        $this->newLine();

        try {
            $admins = User::where('role', 'admin')->get();

            if ($admins->isEmpty()) {
                $this->warn('⚠️  No admin users found. Create an admin first.');
            } else {
                // Check if there's already a super admin
                $existingSuperAdmin = User::where('is_super_admin', true)->first();
                if ($existingSuperAdmin) {
                    $this->info("ℹ️  Current Super Admin: {$existingSuperAdmin->name} ({$existingSuperAdmin->email})");

                    if (!$this->confirm('Do you want to change the Super Admin?', false)) {
                        $this->info('✅ Keeping existing Super Admin.');
                    } else {
                        $this->promptForSuperAdmin($admins, $existingSuperAdmin);
                    }
                } else {
                    if ($admins->count() === 1) {
                        // Only one admin, auto-promote
                        $admin = $admins->first();
                        if ($this->confirm("Promote '{$admin->name}' ({$admin->email}) to Super Admin?", true)) {
                            $admin->is_super_admin = true;
                            $admin->save();
                            $this->info("✅ {$admin->name} is now a Super Admin!");
                        }
                    } else {
                        $this->promptForSuperAdmin($admins);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->warn('⚠️  Could not setup Super Admin: ' . $e->getMessage());
        }

        // 4. Clear config cache
        $this->call('config:clear');
        $this->info('✅ Config cache cleared');

        $this->newLine();
        $this->info('🎉 Upgrade Complete!');
        $this->info('   You can now manage multiple branches from Admin → Branches');
        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Prompt user to select a super admin from list of admins.
     */
    protected function promptForSuperAdmin($admins, $existingSuperAdmin = null)
    {
        $choices = [];
        foreach ($admins as $admin) {
            $marker = $admin->is_super_admin ? ' [CURRENT]' : '';
            $choices[$admin->id] = "{$admin->name} ({$admin->email}){$marker}";
        }

        $selectedId = $this->choice(
            'Select which admin to promote to Super Admin:',
            $choices,
            $existingSuperAdmin?->id
        );

        // Get the actual ID from the choice
        $adminId = array_search($selectedId, $choices);
        if (!$adminId) {
            // User selected by value, find the ID
            foreach ($choices as $id => $label) {
                if ($label === $selectedId) {
                    $adminId = $id;
                    break;
                }
            }
        }

        if ($adminId) {
            // Remove super admin from existing
            if ($existingSuperAdmin) {
                $existingSuperAdmin->is_super_admin = false;
                $existingSuperAdmin->save();
            }

            // Promote new super admin
            $newSuperAdmin = User::find($adminId);
            if ($newSuperAdmin) {
                $newSuperAdmin->is_super_admin = true;
                $newSuperAdmin->save();
                $this->info("✅ {$newSuperAdmin->name} is now the Super Admin!");
            }
        }
    }
}
