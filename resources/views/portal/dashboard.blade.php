<x-portal-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Health Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Welcome -->
            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Welcome back, {{ $patient->name }}!</h3>
                    <p class="mt-1 text-sm text-gray-600">Here is an overview of your health records and rewards.</p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Loyalty Points -->
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="p-3 bg-white bg-opacity-20 rounded-full">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold opacity-90">Loyalty Points</h4>
                            <p class="text-3xl font-bold">{{ $loyaltyPoints }}</p>
                            <p class="text-xs opacity-75 mt-1">Redeemable for discounts</p>
                        </div>
                    </div>
                </div>

                <!-- Visits -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-700">Total Visits</h4>
                            <p class="text-3xl font-bold text-gray-900">{{ $visitCount }}</p>
                            <p class="text-xs text-gray-500 mt-1">Recorded sales/visits</p>
                        </div>
                    </div>
                </div>

                <!-- Prescriptions -->
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-700">Prescriptions</h4>
                            <p class="text-3xl font-bold text-gray-900">{{ $prescriptionCount }}</p>
                            <p class="text-xs text-gray-500 mt-1">Total medications dispensed</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                    @if($recentActivity->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Items</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentActivity as $sale)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $sale->created_at->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ number_format($sale->total_amount, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ ucfirst($sale->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $sale->items->count() }} Items
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No recent activity found.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-portal-layout>