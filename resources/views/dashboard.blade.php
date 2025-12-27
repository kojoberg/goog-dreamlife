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
                <div
                    class="bg-gradient-to-r from-emerald-500 to-green-600 overflow-hidden shadow-lg sm:rounded-xl p-6 text-white transform hover:-translate-y-1 transition duration-300">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-green-100 text-sm font-semibold uppercase tracking-wide">Total Sales Today
                            </div>
                            <div class="mt-2 text-3xl font-extrabold">GHS {{ number_format($todaySales, 2) }}</div>
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Expired Batches -->
                <a href="{{ route('inventory.index', ['filter' => 'expired']) }}"
                    class="block bg-white overflow-hidden shadow-lg sm:rounded-xl p-6 border-l-4 {{ $expiredBatches > 0 ? 'border-red-500' : 'border-gray-300' }} hover:shadow-xl hover:-translate-y-1 transition duration-300 group">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-gray-500 text-sm font-medium uppercase group-hover:text-gray-700">Expiring
                                / Expired</div>
                            <div
                                class="mt-2 text-3xl font-bold {{ $expiredBatches > 0 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $expiredBatches }}
                            </div>
                        </div>
                        <div
                            class="{{ $expiredBatches > 0 ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-400' }} p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Low Stock -->
                <a href="{{ route('products.index', ['filter' => 'low_stock']) }}"
                    class="block bg-white overflow-hidden shadow-lg sm:rounded-xl p-6 border-l-4 {{ $lowStockCount > 0 ? 'border-yellow-500' : 'border-gray-300' }} hover:shadow-xl hover:-translate-y-1 transition duration-300 group">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-gray-500 text-sm font-medium uppercase group-hover:text-gray-700">Low Stock
                                Alerts</div>
                            <div
                                class="mt-2 text-3xl font-bold {{ $lowStockCount > 0 ? 'text-yellow-600' : 'text-gray-900' }}">
                                {{ $lowStockCount }}
                            </div>
                        </div>
                        <div
                            class="{{ $lowStockCount > 0 ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-400' }} p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
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

        // Gradient Fill
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)'); // Indigo
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($displayDates),
                datasets: [{
                    label: 'Sales (GHS)',
                    data: @json($totals),
                    borderColor: '#4f46e5', // Indigo 600
                    backgroundColor: gradient,
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-GH', { style: 'currency', currency: 'GHS' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false,
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function (value) {
                                return value; // Simplified
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>