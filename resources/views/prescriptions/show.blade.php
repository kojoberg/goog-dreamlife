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
                        <div class="flex flex-col items-end gap-2">
                            <span
                                class="px-3 py-1 rounded-full font-bold {{ $prescription->status === 'dispensed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ strtoupper($prescription->status) }}
                            </span>
                            @if($prescription->sale)
                                <a href="{{ route('pos.receipt', $prescription->sale->id) }}" target="_blank"
                                    class="text-sm text-blue-600 hover:underline font-bold">
                                    View Receipt
                                </a>
                            @endif
                        </div>
                    </div>

                    @if(session('success_sale_id'))
                        <div class="mb-4 flex justify-end">
                            <a href="{{ route('pos.receipt', session('success_sale_id')) }}" target="_blank"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                                Print Receipt
                            </a>
                        </div>
                    @endif

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
                        <div class="mt-6 border-t pt-4" x-data="{
                                            pointsRedeeming: 0,
                                            pointValue: {{ $settings->loyalty_point_value ?? 0 }},
                                            maxPoints: {{ $prescription->patient->loyalty_points ?? 0 }},
                                            billTotal: {{ $estimatedTotal }},
                                            get discountValue() { return (this.pointsRedeeming * this.pointValue); },
                                            get finalPayable() { return Math.max(0, this.billTotal - this.discountValue); }
                                        }">
                            <h4 class="font-bold mb-3 text-lg">Dispense & Payment</h4>

                            <!-- Billing Summary -->
                            <div class="bg-gray-50 p-4 rounded mb-4 text-sm">
                                <div class="flex justify-between mb-1">
                                    <span>Subtotal:</span>
                                    <span class="font-bold">GHS {{ number_format($estimatedSubtotal, 2) }}</span>
                                </div>
                                @if($estimatedTax > 0)
                                    @foreach($taxBreakdown as $taxCode => $taxData)
                                        <div class="flex justify-between mb-1 text-gray-600">
                                            <span>{{ $taxData['name'] ?? strtoupper($taxCode) }}
                                                ({{ $taxData['percentage'] ?? 0 }}%):</span>
                                            <span>GHS {{ number_format($taxData['amount'] ?? 0, 2) }}</span>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="flex justify-between mb-1 text-green-700" x-show="discountValue > 0">
                                    <span>Loyalty Discount (<span x-text="pointsRedeeming"></span> pts):</span>
                                    <span class="font-bold">- GHS <span x-text="discountValue.toFixed(2)"></span></span>
                                </div>
                                <div class="flex justify-between border-t border-gray-300 pt-1 mt-1 text-base">
                                    <span>Total Payable:</span>
                                    <span class="font-bold">GHS <span x-text="finalPayable.toFixed(2)"></span></span>
                                </div>
                            </div>

                            <form action="{{ route('prescriptions.dispense', $prescription) }}" method="POST"
                                onsubmit="return confirm('Confirm dispense? This will deduct stock and record a sale.');">
                                @csrf

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <!-- Payment Method -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                                        <select name="payment_method"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required>
                                            <option value="cash">Cash</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="card">Card</option>
                                        </select>
                                    </div>

                                    <!-- Loyalty Redemption -->
                                    @if(($prescription->patient->loyalty_points ?? 0) > 0)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Redeem Points (Max: {{ $prescription->patient->loyalty_points }})
                                            </label>
                                            <div class="flex items-center gap-2">
                                                <input type="number" name="points_redeemed" x-model="pointsRedeeming" min="0"
                                                    :max="maxPoints"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <span class="text-xs text-gray-500 whitespace-nowrap">
                                                    Value: GHS <span x-text="discountValue.toFixed(2)"></span>
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded shadow">
                                    Complete Dispense & Sale
                                </button>
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