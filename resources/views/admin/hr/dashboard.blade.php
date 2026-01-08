<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('HR Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Key Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Total Staff -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500">Total Staff</div>
                    <div class="mt-2 flex items-baseline">
                        <div class="text-3xl font-semibold text-gray-900">{{ $totalEmployees }}</div>
                        <div class="ml-2 text-sm font-medium text-gray-500">Active</div>
                    </div>
                </div>

                <!-- Last Payroll -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500">Last Payroll ({{ $latestPayrollMonth ?? 'N/A' }})
                    </div>
                    <div class="mt-2 flex items-baseline">
                        <div class="text-2xl font-semibold text-gray-900">GHS {{ number_format($lastPayrollCost, 2) }}
                        </div>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $lastPayrollCount }} employees paid</div>
                </div>

                <!-- Pending Appraisals -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-sm font-medium text-gray-500">Appraisals ({{ date('M') }})</div>
                    <div class="mt-2 flex items-baseline">
                        <div class="text-3xl font-semibold text-gray-900">{{ $appraisalsCount }}</div>
                        <div class="ml-2 text-sm font-medium text-gray-500">Done</div>
                    </div>
                    <div class="text-xs text-red-500 mt-1">{{ $pendingAppraisals }} Pending</div>
                </div>

                <!-- My Messages -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-sm font-medium text-gray-500">Unread Messages</div>
                    <div class="mt-2 flex items-baseline">
                        <div class="text-3xl font-semibold text-gray-900">
                            {{ $recentMessages->where('is_read', false)->count() }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Staff Distribution -->
                <x-card>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Staff Distribution</h3>
                    <div class="overflow-y-auto max-h-64">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Count
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($employeesByRole as $role => $count)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ ucfirst($role) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                            {{ $count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card>

                <!-- Recent Messages -->
                <x-card>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Messages</h3>
                        <a href="{{ route('admin.hr.communication.index') }}"
                            class="text-sm text-indigo-600 hover:text-indigo-900">View All</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($recentMessages as $msg)
                            <div class="border-b pb-3 last:border-0 last:pb-0">
                                <div class="flex justify-between">
                                    <div class="text-sm font-medium text-gray-900">{{ $msg->sender->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $msg->created_at->diffForHumans() }}</div>
                                </div>
                                <p class="text-sm text-gray-600 mt-1 truncate">{{ $msg->subject }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No messages found.</p>
                        @endforelse
                    </div>
                </x-card>
            </div>

        </div>
    </div>
</x-app-layout>