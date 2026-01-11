<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Loyalty History: ') . $patient->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Current Balance</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $patient->loyalty_points }} pts</p>
                    </div>
                    <a href="{{ route('patients.show', $patient) }}" class="text-gray-600 hover:text-gray-900">Back to
                        Patient</a>
                </div>

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
                                    Type</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Points</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($loyaltyTransactions as $transaction)
                                <tr class="{{ $transaction['is_reversal'] ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($transaction['date'])->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        @if($transaction['is_reversal'])
                                            <span class="text-red-600">
                                                🔄 Refund (Sale #{{ str_pad($transaction['id'], 6, '0', STR_PAD_LEFT) }})
                                            </span>
                                        @else
                                            <a href="{{ route('pos.receipt', $transaction['id']) }}"
                                                class="text-blue-600 hover:underline">
                                                #{{ str_pad($transaction['id'], 6, '0', STR_PAD_LEFT) }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($transaction['is_reversal'])
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Reversed
                                            </span>
                                        @elseif($transaction['points_earned'] > 0 && $transaction['points_redeemed'] > 0)
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Mixed
                                            </span>
                                        @elseif($transaction['points_earned'] > 0)
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Earned
                                            </span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                                Redeemed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold">
                                        @if($transaction['is_reversal'])
                                            {{-- Reversal: earned points were taken back, redeemed points were returned --}}
                                            @if($transaction['points_earned'] < 0)
                                                <span class="text-red-600">{{ $transaction['points_earned'] }}</span>
                                            @endif
                                            @if($transaction['points_redeemed'] > 0)
                                                <span class="text-green-600 ml-1">+{{ $transaction['points_redeemed'] }}</span>
                                            @endif
                                        @else
                                            @if($transaction['points_earned'] > 0)
                                                <span class="text-green-600">+{{ $transaction['points_earned'] }}</span>
                                            @endif
                                            @if($transaction['points_redeemed'] > 0)
                                                <span class="text-red-600">-{{ $transaction['points_redeemed'] }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                        GHS {{ number_format($transaction['amount'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No loyalty history found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $loyaltyTransactions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>