<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shift Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if($openShift)
                        <!-- Close Shift View -->
                        <h3 class="text-lg font-bold mb-4">Close Shift</h3>
                        <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-4">
                            <p><strong>Shift Started:</strong> {{ $openShift->start_time->format('d M Y, h:i A') }}</p>
                            <p><strong>Starting Cash:</strong> GHS {{ number_format($openShift->starting_cash, 2) }}</p>
                            <!-- Ideal scenario: Show expected cash here? Or keep it hidden for blind count? -->
                            <!-- Let's keep it simple: Show sales so far -->
                            <p class="mt-2"><strong>Total Sales (Approx):</strong> GHS
                                {{ number_format($openShift->sales()->sum('total_amount'), 2) }}</p>
                        </div>

                        <form action="{{ route('shifts.update', $openShift) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="actual_cash" class="block text-gray-700 text-sm font-bold mb-2">Closing Cash
                                    Count (GHS)</label>
                                <input type="number" step="0.01" name="actual_cash" id="actual_cash"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required>
                                <p class="text-xs text-gray-500 mt-1">Please count all cash in the drawer and enter the
                                    total.</p>
                            </div>

                            <div class="mb-4">
                                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                                <textarea name="notes" id="notes"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                            </div>

                            <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                                Close Shift & Logout
                            </button>
                        </form>

                        <div class="mt-8 pt-4 border-t">
                            <a href="{{ route('pos.index') }}" class="text-blue-600 hover:text-blue-800 font-bold">&larr;
                                Back to POS</a>
                        </div>
                    @else
                        <!-- Open Shift View -->
                        <h3 class="text-lg font-bold mb-4">Open Shift</h3>
                        <p class="mb-4 text-gray-600">You must open a shift before making sales.</p>

                        <form action="{{ route('shifts.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="starting_cash" class="block text-gray-700 text-sm font-bold mb-2">Starting Cash
                                    / Float (GHS)</label>
                                <input type="number" step="0.01" name="starting_cash" id="starting_cash" value="0.00"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required>
                            </div>

                            <button type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                                Open Shift
                            </button>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>