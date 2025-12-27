<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Audit Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Filters -->
                    <form method="GET" action="{{ route('audit-logs.index') }}"
                        class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-bold mb-1">User</label>
                            <select name="user_id" class="w-full border rounded p-2">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">Table / Module</label>
                            <input type="text" name="table_name" value="{{ request('table_name') }}"
                                placeholder="e.g. sales" class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">Date</label>
                            <input type="date" name="date" value="{{ request('date') }}"
                                class="w-full border rounded p-2">
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded font-bold hover:bg-blue-700">Filter</button>
                            <a href="{{ route('audit-logs.index') }}"
                                class="ml-2 text-gray-500 hover:underline px-4 py-2">Reset</a>
                        </div>
                    </form>

                    <!-- Logs Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Entity
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Changes
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            {{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap font-semibold">
                                            {{ $log->user->name ?? 'System/Guest' }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $log->action === 'Created' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $log->action === 'Updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $log->action === 'Deleted' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            {{ ucfirst($log->table_name) }} #{{ $log->record_id }}
                                        </td>
                                        <td class="px-4 py-2" x-data="{ open: false }">
                                            <button @click="open = !open" class="text-blue-600 hover:text-blue-800 text-xs">
                                                View Details
                                            </button>
                                            <div x-show="open"
                                                class="mt-2 p-2 bg-gray-100 rounded text-xs font-mono overflow-auto max-w-xs"
                                                style="display: none;">
                                                @if($log->old_values)
                                                    <div class="mb-1 text-red-600">Old: {{ $log->old_values }}</div>
                                                @endif
                                                @if($log->new_values)
                                                    <div class="text-green-600">New: {{ $log->new_values }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-gray-500 text-xs">{{ $log->ip_address }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">No audit logs found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>