<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $openShift ? __('Close Shift') : __('Open Shift') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="max-w-md mx-auto bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if(!$openShift)
                    <!-- OPEN SHIFT FORM -->
                    <form action="{{ route('shifts.store') }}" method="POST">
                        @csrf
                        <div class="text-center mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Start Your Shift</h3>
                            <p class="text-sm text-gray-500">Record your shift start time.</p>
                        </div>

                        {{-- Show Cash Input if User is Cashier OR Branch has NO dedicated cashier (User manages drawer)
                        --}}
                        @if(Auth::user()->role === 'cashier' || (Auth::user()->branch && !Auth::user()->branch->has_cashier))
                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-2">Starting Cash (Drawer Float)</label>
                                <input type="number" step="0.01" name="starting_cash"
                                    class="w-full border rounded p-2 text-center text-xl font-bold" required autofocus
                                    placeholder="0.00">
                                <p class="text-xs text-gray-500 mt-1">Enter the total cash currently in the drawer.</p>
                            </div>
                        @else
                            <p class="text-sm text-yellow-700 mt-2">
                                As a <strong>{{ ucfirst(Auth::user()->role) }}</strong>, you are not managing the cash drawer
                                directly. Click below to start your shift.
                            </p>
                        @endif

                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg">
                            Open Shift
                        </button>
                    </form>
                @else
                    <!-- CLOSE SHIFT FORM -->
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Close Shift</h3>
                        <p class="text-sm text-gray-500">Shift started at: {{ $openShift->start_time->format('H:i') }}</p>
                    </div>

                    @if(Auth::user()->role === 'cashier' || (Auth::user()->branch && !Auth::user()->branch->has_cashier))
                        <div class="bg-gray-50 p-4 rounded mb-6 text-sm">
                            <div class="flex justify-between mb-2">
                                <span>Starting Cash:</span>
                                <span class="font-bold">GHS {{ number_format($openShift->starting_cash, 2) }}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span>Total Sales (Cash):</span>
                                <span class="font-bold text-green-600">GHS {{ number_format($salesTotal, 2) }}</span>
                            </div>
                            <div class="border-t pt-2 flex justify-between">
                                <span>Expected Cash:</span>
                                <span class="font-bold text-blue-600">GHS
                                    {{ number_format($openShift->starting_cash + $salesTotal, 2) }}</span>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('shifts.update', $openShift) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if(Auth::user()->role === 'cashier' || (Auth::user()->branch && !Auth::user()->branch->has_cashier))
                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-2">Actual Cash Count (Drawer)</label>
                                <input type="number" step="0.01" name="actual_cash"
                                    class="w-full border rounded p-2 text-center text-xl font-bold" required>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-2">Notes (Optional)</label>
                                <textarea name="notes" class="w-full border rounded p-2" rows="2"
                                    placeholder="Explain any variance..."></textarea>
                            </div>
                        @else
                            <div class="bg-yellow-50 p-4 rounded mb-4 text-yellow-800 text-sm">
                                Ready to close your shift? ensure all your tasks are completed.
                            </div>
                        @endif

                        <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg"
                            onclick="return confirm('Are you sure you want to close this shift?')">
                            Close Shift
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>