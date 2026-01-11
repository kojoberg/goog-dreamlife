<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;

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
        // Register Google Drive Filesystem Adapter
        try {
            Storage::extend('google', function ($app, $config) {
                $options = [];

                if (!empty($config['teamDriveId'])) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folderId'] ?? null, $options);
                $driver = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch (\Exception $e) {
            // Google Drive not configured or package not available
        }

        \App\Models\InventoryBatch::observe(\App\Observers\InventoryObserver::class);

        // Runtime Debug Mode Override
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::find(1);
                if ($settings && $settings->debug_mode) {
                    config(['app.debug' => true]);
                }
            }
        } catch (\Exception $e) {
            // connection might fail during install/migrations
        }

        // Audit Logging
        \App\Models\User::observe(\App\Observers\AuditObserver::class);
        \App\Models\Product::observe(\App\Observers\AuditObserver::class);
        \App\Models\Sale::observe(\App\Observers\AuditObserver::class);
        \App\Models\Prescription::observe(\App\Observers\AuditObserver::class);
        \App\Models\Patient::observe(\App\Observers\AuditObserver::class);
        \App\Models\Setting::observe(\App\Observers\AuditObserver::class);
        \App\Models\InventoryBatch::observe(\App\Observers\AuditObserver::class);

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
                    'mail.from.address' => $settings->smtp_from_address ?? 'info@uvitech.com',
                    'mail.from.name' => $settings->smtp_from_name ?? 'UVITECH Healthcare',
                ]);
            }

            // Google Drive Configuration
            if ($settings && $settings->google_drive_client_id && $settings->google_drive_refresh_token) {
                config([
                    'filesystems.disks.google' => [
                        'driver' => 'google',
                        'clientId' => $settings->google_drive_client_id,
                        'clientSecret' => $settings->google_drive_client_secret,
                        'refreshToken' => $settings->google_drive_refresh_token,
                        'folderId' => $settings->google_drive_folder_id ?? null,
                    ]
                ]);
            }
        }
    }
}
