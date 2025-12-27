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

                </div>
            </div>
        </div>
    </div>
</x-app-layout>