<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Financial Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Sales Report Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-2">Sales Report</h3>
                    <p class="text-gray-600 mb-4">View detailed sales history, filter by date range, and analyze
                        revenue.</p>
                    <a href="{{ route('admin.financials.sales') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        View Sales
                    </a>
                </div>

                <!-- Inventory Value Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-2">Inventory Analysis</h3>
                    <p class="text-gray-600 mb-4">Current stock valuation, cost analysis, and inventory movement
                        overview.</p>
                    <a href="{{ route('admin.financials.inventory') }}"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        View Inventory
                    </a>
                </div>

                <!-- Profit & Loss Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-2">Gross Profit</h3>
                    <p class="text-gray-600 mb-4">Analyze profitability by comparing revenue against cost of goods sold
                        based on sales.</p>
                    <a href="{{ route('admin.financials.profit') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        View Profit
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>