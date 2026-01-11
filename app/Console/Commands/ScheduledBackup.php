<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use ZipArchive;

class ScheduledBackup extends Command
{
    protected $signature = 'app:scheduled-backup {--force : Force backup regardless of schedule}';
    protected $description = 'Run scheduled database backup to local and cloud storage';

    public function handle()
    {
        $settings = \App\Models\Setting::first();

        if (!$settings) {
            $this->error('Settings not found.');
            return 1;
        }

        $schedule = $settings->backup_schedule ?? 'disabled';

        // Check if backup should run
        if (!$this->option('force') && $schedule === 'disabled') {
            $this->info('Backup schedule is disabled.');
            return 0;
        }

        if (!$this->option('force') && !$this->shouldRunBackup($settings)) {
            $this->info('Backup not due yet.');
            return 0;
        }

        $this->info('Starting scheduled backup...');

        // Create backup directory
        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }

        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.zip';
        $zipPath = storage_path('app/backups/' . $filename);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            $this->error('Failed to create backup zip.');
            return 1;
        }

        // Dump Database
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');
        $dumpFile = storage_path('app/backups/dump.sql');

        $mysqldump = '/opt/homebrew/bin/mysqldump';
        if (!file_exists($mysqldump)) {
            $mysqldump = 'mysqldump';
        }

        $command = "{$mysqldump} --user={$dbUser} --password='{$dbPass}' --host={$dbHost} --no-tablespaces {$dbName} > {$dumpFile} 2>&1";
        system($command, $returnVar);

        if ($returnVar !== 0) {
            \Log::error("Scheduled Backup: mysqldump failed with code $returnVar");
            $this->warn('Database dump failed, continuing with files only.');
        }

        if (file_exists($dumpFile)) {
            $zip->addFile($dumpFile, 'database.sql');
        }

        // Add Public Storage
        $files = \File::allFiles(storage_path('app/public'));
        foreach ($files as $file) {
            $relativePath = 'storage/' . $file->getRelativePathname();
            $zip->addFile($file->getRealPath(), $relativePath);
        }

        $zip->close();

        // Cleanup SQL dump
        if (file_exists($dumpFile)) {
            unlink($dumpFile);
        }

        $this->info("Local backup created: {$filename}");

        // Upload to Google Drive if configured
        if (config('filesystems.disks.google.clientId')) {
            try {
                $fileContent = file_get_contents($zipPath);
                Storage::disk('google')->put($filename, $fileContent);
                $this->info('Backup uploaded to Google Drive.');
            } catch (\Exception $e) {
                \Log::error("Scheduled Backup - Google Drive Failed: " . $e->getMessage());
                $this->warn('Google Drive upload failed: ' . $e->getMessage());
            }
        }

        // Update last backup time
        $settings->update(['last_backup_at' => now()]);

        // Cleanup old backups based on retention policy
        $this->cleanupOldBackups($settings->backup_retention_days ?? 30);

        $this->info('Scheduled backup completed successfully!');
        return 0;
    }

    protected function shouldRunBackup($settings): bool
    {
        $lastBackup = $settings->last_backup_at ? Carbon::parse($settings->last_backup_at) : null;
        $schedule = $settings->backup_schedule;
        $backupTime = $settings->backup_time ?? '02:00';

        $now = now();
        $scheduledTime = Carbon::parse($backupTime);

        // If no previous backup, run it
        if (!$lastBackup) {
            return $now->format('H:i') >= $backupTime;
        }

        switch ($schedule) {
            case 'daily':
                // Run if last backup was before today's scheduled time
                $todayScheduled = $now->copy()->setTimeFromTimeString($backupTime);
                return $now >= $todayScheduled && $lastBackup < $todayScheduled;

            case 'weekly':
                $backupDay = $settings->backup_day ?? 0; // Sunday
                $targetDay = $now->copy()->startOfWeek()->addDays($backupDay)->setTimeFromTimeString($backupTime);
                if ($now < $targetDay) {
                    $targetDay->subWeek();
                }
                return $lastBackup < $targetDay;

            case 'monthly':
                $backupDay = min($settings->backup_day ?? 1, $now->daysInMonth);
                $targetDay = $now->copy()->startOfMonth()->addDays($backupDay - 1)->setTimeFromTimeString($backupTime);
                if ($now < $targetDay) {
                    $targetDay->subMonth();
                }
                return $lastBackup < $targetDay;

            default:
                return false;
        }
    }

    protected function cleanupOldBackups(int $retentionDays): void
    {
        $files = Storage::files('backups');
        $cutoff = now()->subDays($retentionDays);
        $deleted = 0;

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'zip') {
                continue;
            }

            $lastModified = Carbon::createFromTimestamp(Storage::lastModified($file));
            if ($lastModified < $cutoff) {
                Storage::delete($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("Cleaned up {$deleted} old backup(s).");
        }
    }
}
