<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Backup Manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Status Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Last Backup --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Last Backup</p>
                            <p class="text-lg font-semibold text-gray-900">
                                @if($settings && $settings->last_backup_at)
                                    {{ \Carbon\Carbon::parse($settings->last_backup_at)->diffForHumans() }}
                                @else
                                    Never
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Next Scheduled --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Next Scheduled</p>
                            <p class="text-lg font-semibold text-gray-900">
                                @if($nextBackup)
                                    {{ $nextBackup }}
                                @else
                                    <span class="text-yellow-600">Not scheduled</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Cloud Status --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        @if(config('filesystems.disks.google.clientId') && config('filesystems.disks.google.refreshToken'))
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Google Drive</p>
                                <p class="text-lg font-semibold text-green-600">Connected</p>
                            </div>
                        @else
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Google Drive</p>
                                <p class="text-lg font-semibold text-yellow-600">Not configured</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Backup Now Section --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Manual Backup</h3>
                            <p class="text-sm text-gray-500">Create a backup immediately. Includes database + uploaded
                                files.</p>
                        </div>
                        <form action="{{ route('backups.create') }}" method="POST" class="mt-4 md:mt-0">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-lg transition-transform transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                    </path>
                                </svg>
                                Backup Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Schedule Configuration --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Automatic Backup Schedule</h3>
                    <form action="{{ route('backups.schedule') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Frequency</label>
                                <select name="backup_schedule"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="disabled" {{ ($settings->backup_schedule ?? 'disabled') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                                    <option value="daily" {{ ($settings->backup_schedule ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ ($settings->backup_schedule ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ ($settings->backup_schedule ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Time</label>
                                <input type="time" name="backup_time" value="{{ $settings->backup_time ?? '02:00' }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Day (Weekly: 0=Sun, Monthly:
                                    1-31)</label>
                                <input type="number" name="backup_day" value="{{ $settings->backup_day ?? '' }}" min="0"
                                    max="31" placeholder="Optional"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Keep backups for (days)</label>
                                <input type="number" name="backup_retention_days"
                                    value="{{ $settings->backup_retention_days ?? 30 }}" min="1" max="365"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md">
                                Save Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Backup History --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Backup History</h3>

                    @if(count($backups) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">
                                            File Name</th>
                                        <th
                                            class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">
                                            Size</th>
                                        <th
                                            class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">
                                            Date</th>
                                        <th
                                            class="py-2 px-4 border-b text-right text-xs font-semibold text-gray-600 uppercase">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($backups as $backup)
                                        <tr class="hover:bg-gray-100">
                                            <td class="py-2 px-4 border-b text-sm font-medium text-gray-900">
                                                {{ $backup['filename'] }}</td>
                                            <td class="py-2 px-4 border-b text-sm text-gray-600">{{ $backup['size'] }}</td>
                                            <td class="py-2 px-4 border-b text-sm text-gray-600">
                                                {{ $backup['date']->toDayDateTimeString() }}
                                                ({{ $backup['date']->diffForHumans() }})
                                            </td>
                                            <td class="py-2 px-4 border-b text-right text-sm">
                                                <a href="{{ route('backups.download', $backup['filename']) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 font-bold mr-3">Download</a>
                                                <form action="{{ route('backups.delete', $backup['filename']) }}" method="POST"
                                                    class="inline" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-10 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <p class="text-gray-500">No backups found. Create one to ensure your data is safe.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- How to Restore --}}
            <div class="bg-yellow-50 p-4 rounded-md border-l-4 border-yellow-400">
                <h4 class="text-lg font-bold text-yellow-800 mb-2">How to Restore a Backup</h4>
                <p class="text-sm text-yellow-700">
                    1. <strong>Download</strong> the backup zip file<br>
                    2. Extract the contents<br>
                    3. Import <code>database.sql</code> into your database (phpMyAdmin or command line)<br>
                    4. Copy the <code>storage/</code> folder contents back to <code>storage/app/public</code>
                </p>
            </div>

            {{-- Google Drive Setup Guide --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <details class="group">
                    <summary class="p-6 cursor-pointer hover:bg-gray-50 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">📂 Google Drive Setup Guide</h3>
                            <p class="text-sm text-gray-500">Step-by-step instructions to enable cloud backups</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </summary>
                    <div class="px-6 pb-6 border-t">
                        <div class="prose prose-indigo max-w-none mt-4">
                            <h4 class="text-md font-semibold text-gray-800">Step 1: Create a Google Cloud Project</h4>
                            <ol class="text-sm text-gray-700 list-decimal ml-4 mb-4">
                                <li>Go to <a href="https://console.cloud.google.com/" target="_blank"
                                        class="text-indigo-600 hover:underline">Google Cloud Console</a></li>
                                <li>Create a new project (or select existing)</li>
                                <li>Enable the <strong>Google Drive API</strong> from the API Library</li>
                            </ol>

                            <h4 class="text-md font-semibold text-gray-800">Step 2: Create OAuth Credentials</h4>
                            <ol class="text-sm text-gray-700 list-decimal ml-4 mb-4">
                                <li>Go to <strong>Credentials</strong> → <strong>Create Credentials</strong> →
                                    <strong>OAuth Client ID</strong></li>
                                <li>Configure consent screen if prompted (External, test users)</li>
                                <li>Application type: <strong>Web application</strong></li>
                                <li>Add redirect URI: <code>https://developers.google.com/oauthplayground</code></li>
                                <li>Copy the <strong>Client ID</strong> and <strong>Client Secret</strong></li>
                            </ol>

                            <h4 class="text-md font-semibold text-gray-800">Step 3: Generate Refresh Token</h4>
                            <ol class="text-sm text-gray-700 list-decimal ml-4 mb-4">
                                <li>Go to <a href="https://developers.google.com/oauthplayground" target="_blank"
                                        class="text-indigo-600 hover:underline">Google OAuth Playground</a></li>
                                <li>Click the gear icon ⚙️ (top right) → Check <strong>"Use your own OAuth
                                        credentials"</strong></li>
                                <li>Enter your Client ID and Client Secret</li>
                                <li>In Step 1, select <strong>Google Drive API v3</strong> →
                                    <code>https://www.googleapis.com/auth/drive.file</code></li>
                                <li>Click <strong>Authorize APIs</strong>, sign in, and grant access</li>
                                <li>Click <strong>Exchange authorization code for tokens</strong></li>
                                <li>Copy the <strong>Refresh Token</strong></li>
                            </ol>

                            <h4 class="text-md font-semibold text-gray-800">Step 4: Configure in Settings</h4>
                            <ol class="text-sm text-gray-700 list-decimal ml-4 mb-4">
                                <li>Go to <a href="{{ route('settings.index') }}"
                                        class="text-indigo-600 hover:underline">System Settings</a></li>
                                <li>Scroll to <strong>Google Drive Backup Configuration</strong></li>
                                <li>Enter your Client ID, Client Secret, and Refresh Token</li>
                                <li>Optionally, add a Folder ID (from Drive URL) to store backups in a specific folder
                                </li>
                                <li>Click <strong>Save Settings</strong></li>
                            </ol>

                            <div class="bg-green-50 p-3 rounded-md border border-green-200">
                                <p class="text-sm text-green-700">
                                    <strong>✅ Done!</strong> Your backups will now automatically upload to Google Drive
                                    when created.
                                </p>
                            </div>
                        </div>
                    </div>
                </details>
            </div>

        </div>
    </div>
</x-app-layout>