<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center no-print">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sales Report') }}
            </h2>
            <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded shadow hover:bg-gray-700">
                Print Report
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filter Form (Hidden on Print) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6 no-print">
                <form method="GET" action="{{ route('admin.financials.sales') }}" class="flex gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date"
                            value="{{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date"
                            value="{{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">Filter</button>
                </form>
            </div>

            <!-- Report Content -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 print-container">
                <div class="text-center mb-6 hidden print-block">
                    <h1 class="text-2xl font-bold">Sales Report</h1>
                    <p class="text-gray-600">Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-bold">Summary</h3>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div class="bg-gray-50 p-4 rounded">
                            <span class="block text-gray-500 text-sm">Total Sales Count</span>
                            <span class="text-xl font-bold">{{ $sales->count() }}</span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded">
                            <span class="block text-gray-500 text-sm">Total Revenue</span>
                            <span class="text-xl font-bold text-green-600">${{ number_format($totalSales, 2) }}</span>
                        </div>
                    </div>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cashier</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Items</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sales as $sale)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sale->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $sale->reference_number ?? 'SALE-' . $sale->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sale->user->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->items->count() }}
                                    items</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold">
                                    ${{ number_format($sale->total_amount, 2) }}</td>
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

            .upload-hidden {
                display: none;
            }
        }

        .print-block {
            display: none;
        }
    </style>
</x-app-layout>