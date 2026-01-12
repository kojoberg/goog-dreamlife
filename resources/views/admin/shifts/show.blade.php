<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Shift Details') }} #{{ $shift->id }}
            </h2>
            <a href="{{ route('shifts.print', $shift) }}" target="_blank"
                class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">
                Print Report
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Summary Card -->
                <x-card
                    class="col-span-1 border-t-4 {{ abs($variance) > 0.01 ? 'border-red-500' : 'border-green-500' }}">
                    <h3 class="font-bold text-lg mb-4">{{ $shift->user->name }}</h3>
                    <div class="text-sm text-gray-500 mb-4">{{ ucfirst($shift->user->role) }}</div>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Start Time:</span>
                            <span class="font-bold">{{ $shift->start_time->format('M d, H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">End Time:</span>
                            <span
                                class="font-bold">{{ $shift->end_time ? $shift->end_time->format('M d, H:i') : 'Active' }}</span>
                        </div>
                        <hr>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Starting Cash:</span>
                            <span>{{ number_format($shift->starting_cash, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Expected Cash:</span>
                            <span>{{ number_format($shift->expected_cash, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-800">Actual Cash:</span>
                            <span>{{ number_format($shift->actual_cash, 2) }}</span>
                        </div>
                        @if($shift->end_time && abs($variance) > 0.01)
                            <div class="bg-red-50 p-3 rounded text-red-800 font-bold text-center">
                                Variance: {{ $variance > 0 ? '+' : '' }}{{ number_format($variance, 2) }}
                            </div>
                        @elseif($shift->end_time)
                            <div class="bg-green-50 p-3 rounded text-green-800 font-bold text-center">
                                Perfectly Balanced
                            </div>
                        @endif

                        @if($shift->notes)
                            <div class="mt-4">
                                <span class="block text-xs font-bold text-gray-500 uppercase">Notes</span>
                                <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded">{{ $shift->notes }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>

                <!-- Breakdown & Transactions -->
                <div class="col-span-1 md:col-span-2 space-y-6">
                    <x-card>
                        <h3 class="font-bold text-lg mb-4">Sales Breakdown</h3>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="p-2 bg-gray-50 rounded">
                                <span class="block text-gray-500 text-xs">Cash Sales</span>
                                <span class="font-bold text-lg">{{ number_format($cashSales, 2) }}</span>
                            </div>
                            <div class="p-2 bg-gray-50 rounded">
                                <span class="block text-gray-500 text-xs">Card Sales</span>
                                <span class="font-bold text-lg">{{ number_format($cardSales, 2) }}</span>
                            </div>
                            <div class="p-2 bg-gray-50 rounded">
                                <span class="block text-gray-500 text-xs">Mobile Money</span>
                                <span class="font-bold text-lg">{{ number_format($momoSales, 2) }}</span>
                            </div>
                        </div>
                    </x-card>

                    <x-card>
                        <h3 class="font-bold text-lg mb-4">Transactions</h3>
                        <div class="overflow-x-auto max-h-96">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Time</th>
                                        <th class="px-4 py-2 text-left">Reference</th>
                                        <th class="px-4 py-2 text-left">Method</th>
                                        <th class="px-4 py-2 text-right">Amount</th>
                                        <th class="px-4 py-2 text-center">View</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($shiftSales as $sale)
                                        <tr>
                                            <td class="px-4 py-2">{{ $sale->created_at->format('H:i:s') }}</td>
                                            <td class="px-4 py-2 font-mono text-xs">{{ $sale->reference }}</td>
                                            <td class="px-4 py-2 capitalize">{{ $sale->payment_method }}</td>
                                            <td class="px-4 py-2 text-right">{{ number_format($sale->total_amount, 2) }}
                                            </td>
                                            <td class="px-4 py-2 text-center">
                                                <a href="{{ route('sales.show', $sale) }}"
                                                    class="text-indigo-600 hover:text-indigo-900" target="_blank">
                                                    &nearr;
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">No transactions
                                                found for this shift.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </x-card>
                </div>
            </div>

            <a href="{{ route('admin.shifts.index') }}" class="text-blue-600 hover:underline">&larr; Back to Shift
                Reports</a>
        </div>
    </div>
</x-app-layout>