<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Backup Manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">System Backups</h3>
                        
                        <!-- Google Drive Status -->
                        <div class="flex items-center space-x-2">
                             @if(config('filesystems.disks.google.clientId') && config('filesystems.disks.google.clientSecret'))
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm-7 4a1 1 0 112 0 1 1 0 01-2 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                    Google Drive Connected
                                </span>
                             @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-0.5 rounded flex items-center" title="Configure in .env">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Google Drive Not Configured
                                </span>
                             @endif
                        </div>
                    </div>

                    <div class="flex justify-end mb-4">
                        <form action="{{ route('backups.create') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                                    </path>
                                </svg>
                                Create New Backup
                            </button>
                        </form>
                    </div>

                    @if(count($backups) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            File Name</th>
                                        <th
                                            class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Size</th>
                                        <th
                                            class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Date</th>
                                        <th
                                            class="py-2 px-4 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
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
                                                ({{ $backup['date']->diffForHumans() }})</td>
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

                    <div class="mt-8 bg-yellow-50 p-4 rounded-md border-l-4 border-yellow-400">
                        <h4 class="text-lg font-bold text-yellow-800 mb-2">How to Restore?</h4>
                        <p class="text-sm text-yellow-700">
                            To restore a backup, <strong>Download</strong> the zip file.
                            It contains a `database.sql` file and a `storage` folder.
                            <br><br>
                            1. Extract the zip file.<br>
                            2. Import `database.sql` into your database tool (phpMyAdmin or Workbench).<br>
                            3. Copy the `storage` folder contents back to `storage/app/public`.
                        </p>
                    </div>

                    <!-- Cloud Backup Info -->
                    <div class="mt-4 bg-blue-50 p-4 rounded-md border-l-4 border-blue-400">
                        <h4 class="text-lg font-bold text-blue-800 mb-2">Cloud Backup (Google Drive)</h4>
                         @if(config('filesystems.disks.google.clientId'))
                            <p class="text-sm text-blue-700">
                                Cloud backup is <strong>ACTIVE</strong>. Backups created here are automatically uploaded to the configured Google Drive folder.
                            </p>
                        @else
                            <p class="text-sm text-blue-700">
                                Cloud backup is currently <strong>DISABLED</strong>. To enable automatic upload to Google Drive:
                                <ul class="list-disc pl-5 mt-1">
                                    <li>Obtain <code>Client ID</code>, <code>Client Secret</code>, and <code>Refresh Token</code> from Google Cloud Console.</li>
                                    <li>Add the following keys to your <code>.env</code> file:</li>
                                </ul>
                                <div class="bg-gray-800 text-white p-2 rounded mt-2 text-xs font-mono">
                                    GOOGLE_DRIVE_CLIENT_ID=...<br>
                                    GOOGLE_DRIVE_CLIENT_SECRET=...<br>
                                    GOOGLE_DRIVE_REFRESH_TOKEN=...
                                </div>
                            </p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>