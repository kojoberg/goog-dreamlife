<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Analytics Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ tab: 'abc' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tabs -->
            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                    <li class="mr-2">
                        <button @click="tab = 'abc'" :class="{'border-blue-600 text-blue-600': tab === 'abc', 'border-transparent hover:text-gray-600 hover:border-gray-300': tab !== 'abc'}" class="inline-block p-4 rounded-t-lg border-b-2">
                            ABC Inventory Analysis
                        </button>
                    </li>
                    <li class="mr-2">
                        <button @click="tab = 'forecast'" :class="{'border-blue-600 text-blue-600': tab === 'forecast', 'border-transparent hover:text-gray-600 hover:border-gray-300': tab !== 'forecast'}" class="inline-block p-4 rounded-t-lg border-b-2">
                            Sales Forecasting
                        </button>
                    </li>
                </ul>
            </div>

            <!-- ABC Analysis Content -->
            <div x-show="tab === 'abc'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">ABC Analysis (Last 90 Days Revenue)</h3>
                    <p class="mb-4 text-sm text-gray-600">
                        <strong>Category A:</strong> Top 80% contribution. High priority control. <br>
                        <strong>Category B:</strong> Next 15% contribution. Moderate priority. <br>
                        <strong>Category C:</strong> Bottom 5% contribution. Low priority.
                    </p>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Revenue (GHS)</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty Sold</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Cumul. %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($abcData as $data)
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $data['product']->name }}</td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $data['category'] === 'A' ? 'bg-green-100 text-green-800' : ($data['category'] === 'B' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $data['category'] }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">{{ number_format($data['revenue'], 2) }}</td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">{{ $data['quantity'] }}</td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">{{ number_format($data['cumulative_percentage'], 1) }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">No sales data found for ABC analysis.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Forecasting Content -->
            <div x-show="tab === 'forecast'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg" style="display: none;">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Sales Forecast (Next Month)</h3>
                    <p class="mb-4 text-sm text-gray-600">Based on simple moving average of last 3 months quantity sold.</p>

                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Avg Monthly Sales</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Predicted (Next Mo)</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Current Stock</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($forecasts as $forecast)
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $forecast['product']->name }}</td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">{{ $forecast['avg_monthly_sales'] }}</td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right font-bold">{{ $forecast['predicted_next_month'] }}</td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">{{ $forecast['current_stock'] }}</td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            @if($forecast['status'] === 'Risk of Stockout')
                                                <span class="text-red-600 font-bold">⚠️ Risk of Stockout</span>
                                            @elseif($forecast['status'] === 'Overstocked')
                                                <span class="text-yellow-600">Overstocked</span>
                                            @else
                                                <span class="text-green-600">Stable</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">Not enough data for forecasting.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
