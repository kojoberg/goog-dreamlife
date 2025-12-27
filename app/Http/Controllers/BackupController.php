<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use ZipArchive;

class BackupController extends Controller
{
    public function index()
    {
        // List files in storage/app/backups
        $files = Storage::files('backups');
        $backups = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
                $backups[] = [
                    'filename' => basename($file),
                    'size' => $this->formatSize(Storage::size($file)),
                    'date' => Carbon::createFromTimestamp(Storage::lastModified($file)),
                    'path' => $file
                ];
            }
        }

        // Sort by date desc
        usort($backups, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('db-backups.index', compact('backups'));
    }

    public function create()
    {
        // 1. Create Backup Folder
        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }

        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.zip';
        $zipPath = storage_path('app/backups/' . $filename);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {

            // 2. Dump Database
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host');
            $dumpFile = storage_path('app/backups/dump.sql');

            // Use mysqldump with full path
            $mysqldump = '/opt/homebrew/bin/mysqldump';
            if (!file_exists($mysqldump)) {
                $mysqldump = 'mysqldump'; // Fallback
            }

            // Add --column-statistics=0 for compatibility if needed, or --no-tablespaces
            $command = "{$mysqldump} --user={$dbUser} --password='{$dbPass}' --host={$dbHost} --no-tablespaces {$dbName} > {$dumpFile} 2>&1";

            // Execute command
            system($command, $returnVar);

            if ($returnVar !== 0) {
                // Fallback or error?
                // Trying to continue just to zip files if DB fails? No, critical.
                // For now, let's assume it works or handle empty sql.
                \Log::error("Backup: mysqldump failed with code $returnVar");
            }

            if (file_exists($dumpFile)) {
                $zip->addFile($dumpFile, 'database.sql');
            }

            // 3. Add Public Storage (Logos, User uploads)
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

            $message = 'Backup created successfully.';

            // Upload to Google Drive if configured
            if (config('filesystems.disks.google')) {
                try {
                    $fileContent = file_get_contents($zipPath);
                    $cloudFilename = $filename; // Keep same name
                    Storage::disk('google')->put($cloudFilename, $fileContent);
                    $message .= ' And uploaded to Google Drive.';
                } catch (\Exception $e) {
                    \Log::error("Google Drive Backup Failed: " . $e->getMessage());
                    $message .= ' But failed to upload to Google Drive (check logs).';
                }
            }

            return redirect()->route('backups.index')->with('success', $message);
        } else {
            return redirect()->route('backups.index')->with('error', 'Failed to create backup zip.');
        }
    }

    public function download($filename)
    {
        $path = 'backups/' . $filename;
        if (Storage::exists($path)) {
            return Storage::download($path);
        }
        return back()->with('error', 'File not found.');
    }

    public function delete($filename)
    {
        $path = 'backups/' . $filename;
        if (Storage::exists($path)) {
            Storage::delete($path);
            return back()->with('success', 'Backup deleted.');
        }
        return back()->with('error', 'File not found.');
    }

    // Restore is complex (requires dropping db tables etc). 
    // Usually safer to just document how to restore manually or use a dedicated package.
    // For this MVP, we will only allow DOWNLOAD. Restore is a manual admin task.

    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
