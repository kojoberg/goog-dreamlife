<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Sales Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium uppercase">Total Sales Today</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">GHS {{ number_format($todaySales, 2) }}</div>
                </div>

                <!-- Expired Batches -->
                <a href="{{ route('inventory.index', ['filter' => 'expired']) }}"
                    class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 {{ $expiredBatches > 0 ? 'border-red-500 hover:bg-red-50' : 'border-gray-200 hover:bg-gray-50' }} transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-gray-500 text-sm font-medium uppercase">Expiring / Expired Batches</div>
                            <div
                                class="mt-2 text-3xl font-bold {{ $expiredBatches > 0 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $expiredBatches }}
                            </div>
                        </div>
                        @if($expiredBatches > 0)
                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Action
                                Needed</span>
                        @endif
                    </div>
                </a>

                <!-- Low Stock -->
                <a href="{{ route('products.index', ['filter' => 'low_stock']) }}"
                    class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 {{ $lowStockCount > 0 ? 'border-yellow-500 hover:bg-yellow-50' : 'border-gray-200 hover:bg-gray-50' }} transition">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-gray-500 text-sm font-medium uppercase">Low Stock Alerts</div>
                            <div
                                class="mt-2 text-3xl font-bold {{ $lowStockCount > 0 ? 'text-yellow-600' : 'text-gray-900' }}">
                                {{ $lowStockCount }}
                            </div>
                        </div>
                        @if($lowStockCount > 0)
                            <span
                                class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Restock</span>
                        @endif
                    </div>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Chart Area -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Sales Overview (Last 7 Days)</h3>
                    <div class="relative h-72 w-full">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Transactions</h3>
                    <div class="flow-root">
                        <ul role="list" class="-my-5 divide-y divide-gray-200">
                            @forelse($recentSales as $sale)
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                Order #{{ $sale->id }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                by {{ $sale->user->name ?? 'Unknown' }}
                                            </p>
                                        </div>
                                        <div class="inline-flex items-center text-base font-semibold text-gray-900">
                                            GHS {{ number_format($sale->total_amount, 2) }}
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-400 text-right mt-1">
                                        {{ $sale->created_at->diffForHumans() }}
                                    </div>
                                </li>
                            @empty
                                <li class="py-4 text-sm text-gray-500">No recent sales.</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('pos.index') }}"
                            class="w-full flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Go to POS
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($dates),
                datasets: [{
                    label: 'Sales (GHS)',
                    data: @json($totals),
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'GHS ' + value;
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>