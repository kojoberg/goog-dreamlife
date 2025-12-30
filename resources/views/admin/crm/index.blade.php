<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('CRM & Bulk Messaging') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-end mb-6">
                <a href="{{ route('admin.crm.create') }}"
                    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 font-bold">
                    + Create Campaign
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Chart Section -->
                <x-card>
                    <h3 class="font-bold text-lg mb-4">Delivery Report (All Time)</h3>
                    <div style="height: 300px;">
                        <canvas id="deliveryChart"></canvas>
                    </div>
                </x-card>

                <!-- Explanation / Legend -->
                <x-card>
                    <h3 class="font-bold text-lg mb-4">Summary</h3>
                    <ul class="list-disc pl-5 space-y-2">
                        <li><strong>Sent:</strong> {{ $deliveryStats['sent'] }} messages delivered successfully.</li>
                        <li><strong>Failed:</strong> {{ $deliveryStats['failed'] }} messages failed to send.</li>
                        <li><strong>Pending:</strong> {{ $deliveryStats['pending'] }} messages waiting in queue.</li>
                    </ul>
                </x-card>
            </div>

            <x-card>
                <h3 class="font-bold text-lg mb-4">Recent Campaigns</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Target</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($campaigns as $campaign)
                                <tr>
                                    <td class="px-6 py-4 font-medium">{{ $campaign->title }}</td>
                                    <td class="px-6 py-4 uppercase text-xs font-bold
                                            {{ $campaign->type === 'email' ? 'text-blue-600' : 'text-green-600' }}">
                                        {{ $campaign->type }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ ucfirst(str_replace('_', ' ', $campaign->filters['role'] ?? 'All')) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($campaign->status === 'completed') bg-green-100 text-green-800
                                                @elseif($campaign->status === 'failed') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($campaign->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $campaign->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="{{ route('admin.crm.show', $campaign) }}"
                                            class="text-indigo-600 hover:text-indigo-900">View Report</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('deliveryChart');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Sent', 'Failed', 'Pending'],
                datasets: [{
                    data: [{{ $deliveryStats['sent'] }}, {{ $deliveryStats['failed'] }}, {{ $deliveryStats['pending'] }}],
                    backgroundColor: [
                        'rgb(34, 197, 94)', // Green
                        'rgb(239, 68, 68)', // Red
                        'rgb(234, 179, 8)'  // Yellow
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
</x-app-layout>