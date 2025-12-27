<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SystemHealthController extends Controller
{
    public function index()
    {
        $checks = [];

        // 1. Database Check
        try {
            $pdo = DB::connection()->getPdo();
            $checks['database'] = [
                'status' => 'ok',
                'message' => 'Connected to ' . DB::connection()->getDatabaseName(),
                'latency' => 'N/A' // Could measure query time
            ];
        } catch (\Exception $e) {
            $checks['database'] = [
                'status' => 'error',
                'message' => 'Connection Failed: ' . $e->getMessage()
            ];
        }

        // 2. Disk Space (Root)
        try {
            $freeSpace = disk_free_space(base_path());
            $totalSpace = disk_total_space(base_path());
            $freeHuman = $this->formatBytes($freeSpace);
            $totalHuman = $this->formatBytes($totalSpace);

            $checks['disk'] = [
                'status' => ($freeSpace / $totalSpace) > 0.1 ? 'ok' : 'warning',
                'message' => "$freeHuman free of $totalHuman"
            ];
        } catch (\Exception $e) {
            $checks['disk'] = ['status' => 'error', 'message' => $e->getMessage()];
        }

        // 3. Storage Permissions
        $logPath = storage_path('logs');
        $isWritable = is_writable($logPath);
        $checks['storage_logs'] = [
            'status' => $isWritable ? 'ok' : 'error',
            'message' => $isWritable ? 'Writable' : 'Not Writable'
        ];

        // 4. Cache
        try {
            Cache::put('health_check', 'ok', 10);
            $val = Cache::get('health_check');
            $checks['cache'] = [
                'status' => $val === 'ok' ? 'ok' : 'error',
                'message' => $val === 'ok' ? 'Functional' : 'Failed to retrieve value'
            ];
        } catch (\Exception $e) {
            $checks['cache'] = ['status' => 'error', 'message' => $e->getMessage()];
        }

        // 5. External APIs
        $settings = Setting::first();

        // RxNav (Public)
        try {
            $start = microtime(true);
            $response = Http::timeout(3)->get('https://rxnav.nlm.nih.gov/REST/version');
            $duration = round((microtime(true) - $start) * 1000, 2);
            $checks['rxnav'] = [
                'status' => $response->successful() ? 'ok' : 'warning',
                'message' => $response->successful() ? "Online ($duration ms)" : 'Unreachable (' . $response->status() . ')'
            ];
        } catch (\Exception $e) {
            $checks['rxnav'] = ['status' => 'warning', 'message' => 'Unreachable: ' . $e->getMessage()];
        }

        // UelloSend Check (Config only)
        $checks['uello_sms'] = [
            'status' => ($settings && $settings->sms_api_key) ? 'ok' : 'warning',
            'message' => ($settings && $settings->sms_api_key) ? 'Configured' : 'Missing API Key'
        ];

        // Google Drive Check (Config only)
        $checks['google_drive'] = [
            'status' => ($settings && $settings->google_drive_refresh_token) ? 'ok' : 'warning',
            'message' => ($settings && $settings->google_drive_refresh_token) ? 'Configured' : 'Missing Refresh Token'
        ];

        return view('admin.system_health', compact('checks'));
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
