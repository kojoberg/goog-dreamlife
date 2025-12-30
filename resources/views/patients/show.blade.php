<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Patient Profile') }}: {{ $patient->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Patient Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-indigo-700 mb-2">Contact Details</h3>
                        <p><strong>Phone:</strong> {{ $patient->phone ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $patient->email ?? 'N/A' }}</p>
                        <p><strong>Address:</strong> {{ $patient->address ?? 'N/A' }}</p>
                    </div>
                    <div>
                         <h3 class="text-lg font-medium text-red-700 mb-2">Medical Profile</h3>
                         <p><strong>Allergies:</strong> <span class="text-red-600 font-bold">{{ $patient->allergies ?? 'None' }}</span></p>
                         <p><strong>History:</strong> {{ $patient->medical_history ?? 'None' }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabs / Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Prescriptions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Prescription History</h3>
                    <ul class="divide-y divide-gray-200">
                        @forelse($patient->prescriptions as $prescription)
                            <li class="py-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Dr. {{ $prescription->doctor->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">{{ $prescription->created_at->format('M d, Y') }}</div>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $prescription->status === 'dispensed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($prescription->status) }}
                                    </span>
                                </div>
                                <div class="mt-1 text-sm text-gray-600">
                                    @foreach($prescription->items as $item)
                                        <div>- {{ $item->medication_name }} ({{ $item->dosage }})</div>
                                    @endforeach
                                </div>
                            </li>
                        @empty
                            <li class="text-gray-500 text-sm">No prescriptions found.</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Sales History -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Purchase History</h3>
                     <ul class="divide-y divide-gray-200">
                        @forelse($patient->sales->sortByDesc('created_at')->take(5) as $sale)
                            <li class="py-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Order #{{ $sale->id }}</div>
                                        <div class="text-xs text-gray-500">{{ $sale->created_at->format('M d, Y H:i') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold">GHS {{ number_format($sale->total_amount, 2) }}</div>
                                        <div class="text-xs text-gray-500">{{ ucfirst($sale->payment_method) }}</div>
                                    </div>
                                </div>
                            </li>
                        @empty
                             <li class="text-gray-500 text-sm">No purchases found.</li>
                        @endforelse
                     </ul>
                </div>

                <!-- Loyalty History (#9) -->
                <div class="col-span-1 lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center border-b pb-2 mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Loyalty Points History</h3>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('patients.loyalty', $patient) }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">View Full History</a>
                            <span class="bg-indigo-100 text-indigo-800 text-sm font-semibold px-3 py-1 rounded-full">
                                Balance: {{ $patient->loyalty_points }} pts
                            </span>
                        </div>
                    </div>
                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Input</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Change</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Effect</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                             @forelse($patient->sales->where(fn($s) => $s->points_earned > 0 || $s->points_redeemed > 0)->sortByDesc('created_at') as $sale)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $sale->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Purchase (Order #{{ $sale->id }})
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($sale->points_earned > 0)
                                            <span class="text-green-600 font-bold">+{{ $sale->points_earned }}</span>
                                        @endif
                                        @if($sale->points_redeemed > 0)
                                            <span class="text-red-600 font-bold">-{{ $sale->points_redeemed }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($sale->points_redeemed > 0)
                                            Saved GHS {{ number_format($sale->discount_amount, 2) }}
                                        @else
                                            Earned on GHS {{ number_format($sale->total_amount, 2) }}
                                        @endif
                                    </td>
                                </tr>
                             @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No loyalty activity yet.</td>
                                </tr>
                             @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
