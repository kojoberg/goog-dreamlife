<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sale Details') }} #{{ $sale->receipt_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Date: {{ $sale->created_at->format('F j, Y g:i A') }}</p>
                            <p class="text-sm text-gray-500">Cashier: {{ $sale->user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Customer: {{ $sale->patient->name ?? 'Walk-in' }}</p>
                        </div>
                        <div>
                            <a href="{{ route('pos.receipt', $sale) }}" target="_blank"
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-bold flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 001.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008h-.008V10.5zm-3 0h.008v.008h-.008V10.5z" />
                                </svg>
                                Print Receipt
                            </a>
                        </div>
                    </div>

                    <h3 class="font-bold text-lg mb-4">Items Scold</h3>
                    <table class="min-w-full divide-y divide-gray-200 mb-6">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($sale->items as $item)
                                <tr>
                                    <td class="px-6 py-4">{{ $item->product->name ?? 'Unknown Item' }}</td>
                                    <td class="px-6 py-4">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4">GHS {{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-4">GHS {{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-bold">Subtotal</td>
                                <td class="px-6 py-4 font-bold">GHS {{ number_format($sale->subtotal, 2) }}</td>
                            </tr>
                            @if($sale->tax_amount > 0)
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right">Tax</td>
                                    <td class="px-6 py-4">GHS {{ number_format($sale->tax_amount, 2) }}</td>
                                </tr>
                            @endif
                            @if($sale->discount_amount > 0)
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-green-600">Discount / Loyalty</td>
                                    <td class="px-6 py-4 text-green-600">-GHS {{ number_format($sale->discount_amount, 2) }}
                                    </td>
                                </tr>
                            @endif
                            <tr class="bg-gray-100">
                                <td colspan="3" class="px-6 py-4 text-right font-bold text-lg">Total Paid</td>
                                <td class="px-6 py-4 font-bold text-lg">GHS {{ number_format($sale->total_amount, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-gray-500">Payment Method</td>
                                <td class="px-6 py-4 capitalize">{{ str_replace('_', ' ', $sale->payment_method) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    <a href="{{ route('sales.index') }}" class="text-blue-600 hover:underline">&larr; Back to Sales
                        History</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>