<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                $settings = Setting::first();
                // Fallback if no settings exist yet
                if (!$settings) {
                    $settings = new Setting();
                }
                View::share('settings', $settings);
            }
        } catch (\Exception $e) {
            // Fails safe during migrations
        }
    }
}
