<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center no-print">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gross Profit Report') }}
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
                <form method="GET" action="{{ route('admin.financials.profit') }}" class="flex gap-4 items-end">
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
                    <h1 class="text-2xl font-bold">Profit & Loss Statement (Gross)</h1>
                    <p class="text-gray-600">Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                </div>

                <div class="max-w-xl mx-auto">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg text-gray-600">Total Revenue (Sales)</span>
                            <span class="text-xl font-bold text-gray-900">${{ number_format($revenue, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200">
                            <span class="text-lg text-gray-600">Cost of Goods Sold (COGS)</span>
                            <span class="text-xl font-bold text-red-600">-${{ number_format($cogs, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-xl font-bold text-gray-800">Gross Profit</span>
                            <span class="text-2xl font-bold text-green-600">${{ number_format($grossProfit, 2) }}</span>
                        </div>
                    </div>
                </div>

                <p class="text-center text-sm text-gray-500 mt-8">
                    Note: This report calculates Gross Profit based on (Sales Price - Cost Price) of items sold.
                    Operating expenses are not included here.
                </p>
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

            .max-w-xl {
                max-width: 100% !important;
            }
        }

        .print-block {
            display: none;
        }
    </style>
</x-app-layout>