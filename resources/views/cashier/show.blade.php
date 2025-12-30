<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Process Payment') }} #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Invoice Details -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-bold mb-4 border-b pb-2">Invoice Details</h3>

                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <dt class="text-sm font-medium text-gray-500">Patient</dt>
                                <dd class="text-sm text-gray-900">{{ $sale->patient->name ?? 'Walk-in Customer' }}</dd>

                                <dt class="text-sm font-medium text-gray-500">Date/Time</dt>
                                <dd class="text-sm text-gray-900">{{ $sale->created_at->format('d M Y, H:i') }}</dd>

                                <dt class="text-sm font-medium text-gray-500">Pharmacist</dt>
                                <dd class="text-sm text-gray-900">{{ $sale->user->name ?? 'Unknown' }}</dd>
                            </dl>

                            <div class="mt-6">
                                <h4 class="font-medium text-gray-900 mb-2">Items</h4>
                                <ul class="divide-y divide-gray-200 border-t border-b">
                                    @foreach($sale->items as $item)
                                        <li class="py-2 flex justify-between text-sm">
                                            <span>{{ $item->product->name }} (x{{ $item->quantity }})</span>
                                            <span>{{ number_format($item->subtotal, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="mt-2 text-right">
                                    <span class="text-gray-500 text-sm">Total Due:</span>
                                    <span class="text-xl font-bold text-gray-900 ml-2">GHS
                                        {{ number_format($sale->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <div>
                            <form action="{{ route('cashier.update', $sale) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <h3 class="text-lg font-bold mb-4">Payment Collection</h3>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Payment Method</label>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <input type="radio" name="payment_method" value="cash" id="pm_cash" checked
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                            <label for="pm_cash" class="ml-2 block text-sm text-gray-900">Cash</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="radio" name="payment_method" value="mobile_money" id="pm_momo"
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                            <label for="pm_momo" class="ml-2 block text-sm text-gray-900">Mobile
                                                Money</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="radio" name="payment_method" value="card" id="pm_card"
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                            <label for="pm_card" class="ml-2 block text-sm text-gray-900">Card /
                                                POS</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Amount Tendered
                                        (GHS)</label>
                                    <input type="number" step="0.01" name="amount_tendered" id="amount_tendered"
                                        class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-lg"
                                        placeholder="0.00" required min="{{ $sale->total_amount }}">

                                    <div class="mt-2 text-right text-sm text-gray-600" id="change_display"
                                        style="display: none;">
                                        Change: <span class="font-bold text-green-600" id="change_amount">0.00</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <a href="{{ route('cashier.index') }}"
                                        class="text-gray-500 hover:text-gray-700 underline">Cancel</a>
                                    <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                                        Confirm Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputToken = document.getElementById('amount_tendered');
            const changeDisplay = document.getElementById('change_display');
            const changeAmount = document.getElementById('change_amount');
            const total = {{ $sale->total_amount }};

            inputToken.addEventListener('input', function () {
                const val = parseFloat(this.value);
                if (!isNaN(val) && val >= total) {
                    changeDisplay.style.display = 'block';
                    changeAmount.innerText = (val - total).toFixed(2);
                } else {
                    changeDisplay.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>