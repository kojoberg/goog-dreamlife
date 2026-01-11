<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('app:send-refill-reminders')->dailyAt('09:00');

// Scheduled backup - runs hourly, command checks if backup is actually due
Schedule::command('app:scheduled-backup')->hourly();
