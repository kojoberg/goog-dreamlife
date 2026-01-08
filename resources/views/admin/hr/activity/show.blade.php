<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Activity Log') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date/Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Changes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($activities as $log)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $log->action == 'Deleted' ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $log->action }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ class_basename($log->table_name) }} #{{ $log->record_id }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">
                                        <details>
                                            <summary class="cursor-pointer text-indigo-500">View Data</summary>
                                            <pre
                                                class="mt-2 bg-gray-50 p-2 rounded">{{ json_encode(json_decode($log->new_values), JSON_PRETTY_PRINT) }}</pre>
                                        </details>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4 px-4">
                        {{ $activities->links() }}
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>