<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff Activity Tracking') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <!-- Filter -->
                <form method="GET" class="mb-6 flex gap-4 items-end bg-gray-50 p-4 rounded-md">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" value="{{ $start }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date" value="{{ $end }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff Member
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sales
                                    Volume</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Revenue
                                    Generated</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                    Prescriptions</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">System
                                    Actions</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Details
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($activityReport as $row)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $row['user']->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $row['role'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                        {{ number_format($row['sales_count']) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">
                                        GHS {{ number_format($row['sales_revenue'], 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                        {{ number_format($row['rx_count']) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                        {{ number_format($row['system_actions']) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.hr.activity.show', $row['user']) }}"
                                            class="text-indigo-600 hover:text-indigo-900">Log</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>