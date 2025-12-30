<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('View Prescription') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold">Patient: {{ $prescription->patient->name ?? 'Unknown' }}</h3>
                            <p class="text-sm text-gray-500">Dr. {{ $prescription->doctor->name ?? 'Unknown' }}</p>
                            <p class="text-sm text-gray-500">{{ $prescription->created_at->format('F j, Y g:i A') }}</p>
                        </div>
                        <div>
                            <span
                                class="px-3 py-1 rounded-full font-bold {{ $prescription->status === 'dispensed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ strtoupper($prescription->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-bold border-b pb-2 mb-2">Medications</h4>
                        <ul class="list-disc pl-5">
                            @foreach($prescription->medications as $med)
                                <li class="mb-1">
                                    <span class="font-semibold">{{ $med['name'] ?? 'Unknown Drug' }}</span>
                                    - {{ $med['dosage'] ?? '' }}
                                    ({{ $med['frequency'] ?? '' }})
                                    @if(!empty($med['route'])) <span
                                    class="text-xs bg-gray-200 px-1 rounded">{{ $med['route'] }}</span> @endif
                                    @if(!empty($med['days_supply'])) <span
                                    class="text-xs text-blue-600 ml-1">{{ $med['days_supply'] }} Days</span> @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @if($prescription->notes)
                        <div class="mb-6">
                            <h4 class="font-bold border-b pb-2 mb-2">Notes</h4>
                            <p class="text-gray-700">{{ $prescription->notes }}</p>
                        </div>
                    @endif

                    @if($prescription->status === 'pending')
                        <div class="mt-6 border-t pt-4">
                            <form action="{{ route('prescriptions.dispense', $prescription) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to dispense this prescription? Stock will be deduction and a Sale recorded.');">
                                @csrf
                                <div class="flex items-center space-x-2">
                                    <select name="payment_method" class="border-gray-300 rounded-md shadow-sm text-sm"
                                        required>
                                        <option value="cash">Cash</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="card">Card</option>
                                    </select>
                                    <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Dispense & Process Sale
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    @if($prescription->status === 'dispensed')
                        <div class="mt-6 border-t pt-4">
                            <form action="{{ route('prescriptions.refill', $prescription) }}" method="POST"
                                onsubmit="return confirm('Create a new refill prescription based on this one?');">
                                @csrf
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Refill Prescription
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>