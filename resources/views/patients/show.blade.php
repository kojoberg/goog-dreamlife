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
                        @forelse($patient->sales as $sale)
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

            </div>
        </div>
    </div>
</x-app-layout>
