<x-portal-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Loyalty Points History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Points Balance Card -->
            <div class="mb-6 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold opacity-90">Current Points Balance</h3>
                        <p class="text-4xl font-bold mt-2">{{ $patient->loyalty_points }}</p>
                        @if($settings && $settings->loyalty_point_value > 0)
                            <p class="text-sm opacity-75 mt-2">
                                ≈ GHS {{ number_format($patient->loyalty_points * $settings->loyalty_point_value, 2) }}
                                value
                            </p>
                        @endif
                    </div>
                    <div class="p-4 bg-white bg-opacity-20 rounded-full">
                        <svg class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- How It Works -->
            <div class="mb-6 bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">How Loyalty Points Work</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-600 font-bold">1</span>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Earn Points</p>
                            <p class="text-gray-500">Get points with every purchase</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-bold">2</span>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Accumulate</p>
                            <p class="text-gray-500">Points add up over time</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-purple-600 font-bold">3</span>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium text-gray-900">Redeem</p>
                            <p class="text-gray-500">Use points for discounts</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Points History</h3>

                    @if($loyaltyHistory->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Transaction</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Earned</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Redeemed</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($loyaltyHistory as $transaction)
                                        <tr class="{{ $transaction['is_reversal'] ? 'bg-red-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($transaction['date'])->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($transaction['is_reversal'])
                                                    <span class="text-red-600">Refund</span>
                                                @else
                                                    <span class="text-gray-900">Purchase
                                                        #{{ str_pad($transaction['id'], 6, '0', STR_PAD_LEFT) }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                GHS {{ number_format($transaction['amount'], 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($transaction['points_earned'] > 0)
                                                    <span
                                                        class="text-green-600 font-medium">+{{ $transaction['points_earned'] }}</span>
                                                @elseif($transaction['points_earned'] < 0)
                                                    <span
                                                        class="text-red-600 font-medium">{{ $transaction['points_earned'] }}</span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($transaction['points_redeemed'] > 0)
                                                    <span
                                                        class="text-orange-600 font-medium">-{{ $transaction['points_redeemed'] }}</span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $loyaltyHistory->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No loyalty activity yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Make purchases to start earning points!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>