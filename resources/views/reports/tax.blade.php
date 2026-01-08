<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tax Reports & GRA Remittance
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Period Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="mt-1 block border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="mt-1 block border-gray-300 rounded-md shadow-sm">
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Filter
                        </button>
                        <a href="{{ route('admin.tax.rates.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Manage Tax Rates
                        </a>
                    </form>
                </div>
            </div>

            <!-- Tax Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 uppercase">Total Sales</h3>
                        <p class="text-3xl font-bold text-gray-900 mt-2">GHS {{ number_format($totalSales, 2) }}</p>
                    </div>
                </div>
                <div class="bg-green-50 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-green-700 uppercase">Tax Collected</h3>
                        <p class="text-3xl font-bold text-green-900 mt-2">GHS {{ number_format($totalTaxCollected, 2) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 uppercase">Transactions with Tax</h3>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $sales->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Tax Breakdown -->
            @if(!empty($taxBreakdown))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Tax Breakdown by Type</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        @foreach($taxBreakdown as $code => $data)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-500">{{ $data['name'] }}</p>
                            <p class="text-2xl font-bold text-gray-900">GHS {{ number_format($data['amount'], 2) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Record Remittance Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Record Tax Remittance to GRA</h3>
                    <form action="{{ route('admin.tax.remittance.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Period Start</label>
                            <input type="date" name="period_start" value="{{ $startDate }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Period End</label>
                            <input type="date" name="period_end" value="{{ $endDate }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Collected</label>
                            <input type="number" name="total_collected" step="0.01" value="{{ $totalTaxCollected }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount Remitted</label>
                            <input type="number" name="total_remitted" step="0.01" placeholder="0.00" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">GRA Reference #</label>
                            <input type="text" name="reference_number" placeholder="e.g., GRA-2026-001"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full">
                                Record Remittance
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Remittance History -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Remittance History</h3>
                    @if($remittances->isEmpty())
                        <p class="text-gray-500 text-center py-8">No remittance records yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Collected</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remitted</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Outstanding</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">GRA Ref #</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($remittances as $rem)
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                                {{ $rem->period_start->format('M d') }} - {{ $rem->period_end->format('M d, Y') }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap font-medium">GHS {{ number_format($rem->total_collected, 2) }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-green-600 font-medium">GHS {{ number_format($rem->total_remitted, 2) }}</td>
                                            <td class="px-4 py-4 whitespace-nowrap {{ $rem->outstanding > 0 ? 'text-red-600' : 'text-gray-500' }} font-medium">
                                                GHS {{ number_format($rem->outstanding, 2) }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                    {{ $rem->status === 'paid' ? 'bg-green-100 text-green-800' : ($rem->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ ucfirst($rem->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-mono">{{ $rem->reference_number ?? '-' }}</td>
                                            <td class="px-4 py-4 text-sm text-gray-500">{{ Str::limit($rem->notes, 30) ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $remittances->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
