<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center no-print">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inventory Report') }}
            </h2>
            <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded shadow hover:bg-gray-700">
                Print Report
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 print-container">
                <div class="text-center mb-6 hidden print-block">
                    <h1 class="text-2xl font-bold">Inventory Valuation Report</h1>
                    <p class="text-gray-600">Generated on: {{ now()->format('d M Y H:i') }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-blue-50 p-4 rounded">
                        <span class="block text-gray-500 text-sm">Total Retail Value</span>
                        <span class="text-xl font-bold text-blue-700">${{ number_format($totalValue, 2) }}</span>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded">
                        <span class="block text-gray-500 text-sm">Total Cost Value</span>
                        <span class="text-xl font-bold text-yellow-700">${{ number_format($totalCost, 2) }}</span>
                    </div>
                </div>

                <h3 class="text-lg font-bold mb-4">Top Products by Stock Value</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stock Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit Cost</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit Price</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($products->sortByDesc(fn($p) => $p->stock * $p->unit_price)->take(50) as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $product->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                    {{ $product->stock }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                    ${{ number_format($product->cost_price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                    ${{ number_format($product->unit_price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold">
                                    ${{ number_format($product->stock * $product->unit_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .print-block {
                display: block !important;
            }

            body {
                background: white;
            }

            .shadow-sm {
                box-shadow: none !important;
            }
        }

        .print-block {
            display: none;
        }
    </style>
</x-app-layout>