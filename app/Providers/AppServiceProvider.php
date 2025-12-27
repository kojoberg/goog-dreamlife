<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\InventoryBatch::observe(\App\Observers\InventoryObserver::class);

        // Dynamic SMTP Configuration
        if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            $settings = \App\Models\Setting::first();
            if ($settings && $settings->smtp_host) {
                config([
                    'mail.default' => 'smtp', // FORCE usage of SMTP
                    'mail.mailers.smtp.host' => $settings->smtp_host,
                    'mail.mailers.smtp.port' => $settings->smtp_port,
                    'mail.mailers.smtp.username' => $settings->smtp_username,
                    'mail.mailers.smtp.password' => $settings->smtp_password,
                    'mail.mailers.smtp.encryption' => $settings->smtp_encryption,
                    'mail.from.address' => $settings->smtp_from_address ?? 'info@dreamlife.com',
                    'mail.from.name' => $settings->smtp_from_name ?? 'Dream Life Healthcare',
                ]);
            }
        }
    }
}
