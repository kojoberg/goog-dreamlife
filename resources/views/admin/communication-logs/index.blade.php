<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Communication Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                    <p class="text-xs font-bold text-gray-500 uppercase">Total</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                    <p class="text-xs font-bold text-gray-500 uppercase">📱 SMS</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['sms']) }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-purple-500">
                    <p class="text-xs font-bold text-gray-500 uppercase">📧 Email</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['email']) }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
                    <p class="text-xs font-bold text-gray-500 uppercase">Failed</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['failed']) }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.communication-logs.index') }}"
                        class="mb-6 grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div>
                            <label class="block text-sm font-bold mb-1">Type</label>
                            <select name="type" class="w-full border rounded p-2">
                                <option value="">All Types</option>
                                <option value="sms" {{ request('type') == 'sms' ? 'selected' : '' }}>📱 SMS</option>
                                <option value="email" {{ request('type') == 'email' ? 'selected' : '' }}>📧 Email</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">Status</label>
                            <select name="status" class="w-full border rounded p-2">
                                <option value="">All Statuses</option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed
                                </option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">From Date</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">To Date</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Recipient, message..." class="w-full border rounded p-2">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded font-bold hover:bg-blue-700">Filter</button>
                            <a href="{{ route('admin.communication-logs.index') }}"
                                class="text-gray-500 hover:underline px-4 py-2">Reset</a>
                        </div>
                    </form>

                    <!-- Logs Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                        Recipient</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Context
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">User
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Details
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-gray-500">
                                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <span class="text-lg">{{ $log->type_icon }}</span>
                                            <span class="ml-1 text-xs uppercase">{{ $log->type }}</span>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            bg-{{ $log->status_color }}-100 text-{{ $log->status_color }}-800">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap font-mono text-sm">
                                            {{ $log->recipient }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-gray-500">
                                            {{ $log->context ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            {{ $log->user->name ?? 'System' }}
                                            @if($log->branch)
                                                <span class="text-xs text-gray-400">({{ $log->branch->name }})</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2" x-data="{ open: false }">
                                            <button @click="open = !open" class="text-blue-600 hover:text-blue-800 text-xs">
                                                View Details
                                            </button>
                                            <div x-show="open"
                                                class="mt-2 p-3 bg-gray-100 rounded text-xs overflow-auto max-w-md"
                                                style="display: none;">
                                                @if($log->subject)
                                                    <div class="mb-2">
                                                        <strong>Subject:</strong> {{ $log->subject }}
                                                    </div>
                                                @endif
                                                <div class="mb-2">
                                                    <strong>Message:</strong>
                                                    <pre
                                                        class="whitespace-pre-wrap text-gray-700 mt-1">{{ Str::limit($log->message, 300) }}</pre>
                                                </div>
                                                @if($log->response)
                                                    <div class="mt-2 pt-2 border-t">
                                                        <strong
                                                            class="{{ $log->status === 'failed' ? 'text-red-600' : 'text-green-600' }}">Response:</strong>
                                                        <pre
                                                            class="whitespace-pre-wrap text-gray-600 mt-1 font-mono">{{ $log->response }}</pre>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            <div class="text-4xl mb-2">📭</div>
                                            No communication logs found.
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