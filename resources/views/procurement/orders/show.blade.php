<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            PO #{{ $order->id }} - {{ $order->supplier?->name ?? 'Unknown Supplier' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-4">
                        <div class="grid grid-cols-2 gap-8 w-3/4">
                            <div>
                                <p><strong>Status:</strong> <span
                                        class="uppercase font-bold inline-block px-2 py-1 rounded text-xs {{ $order->status === 'received' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $order->status }}</span>
                                </p>
                                <p class="mt-2"><strong>Ordered Date:</strong> {{ $order->created_at->format('d M Y') }}
                                </p>
                                <p><strong>Expected Date:</strong>
                                    {{ $order->expected_date ? $order->expected_date->format('d M Y') : 'N/A' }}</p>
                                @if($order->received_by)
                                    <p><strong>Received By:</strong> {{ $order->received_by }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-gray-800">{{ number_format($order->total_amount, 2) }}
                                </p>
                                <p class="text-sm text-gray-500 uppercase tracking-wide">Total Value</p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('procurement.orders.print', $order) }}" target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Print Order
                            </a>
                        </div>
                    </div>

                    @if($order->notes)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <h4 class="font-bold text-sm text-gray-700 mb-1">Notes:</h4>
                            <p class="text-gray-600 italic bg-gray-50 p-3 rounded border border-gray-100">
                                {{ $order->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="font-bold text-lg mb-4">Items Ordered</h3>
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-3 text-left">Product</th>
                                <th class="p-3 text-right">Ordered</th>
                                <th class="p-3 text-right">Received</th>
                                <th class="p-3 text-right">Unit Cost</th>
                                <th class="p-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr class="border-b">
                                    <td class="p-3">{{ $item->product?->name ?? 'Deleted Product' }}</td>
                                    <td class="p-3 text-right">{{ $item->quantity_ordered }}</td>
                                    <td
                                        class="p-3 text-right font-bold {{ $item->quantity_received < $item->quantity_ordered ? 'text-red-500' : 'text-green-500' }}">
                                        {{ $item->quantity_received }}
                                    </td>
                                    <td class="p-3 text-right">{{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="p-3 text-right">
                                        {{ number_format($item->quantity_ordered * $item->unit_cost, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            @if($order->status !== 'received')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-bold text-lg mb-2">Receive Stock</h3>
                        <p class="mb-4 text-gray-600">Clicking below will receive all pending items into inventory and mark
                            this PO as completed.</p>

                        <form action="{{ route('procurement.orders.receive', $order) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Received By (Staff Name)</label>
                                <input type="text" name="received_by"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required placeholder="Enter name of staff receiving stock">
                            </div>

                            <div class="mb-6">
                                <h4 class="font-bold text-gray-700 mb-3">Item Expiry Dates</h4>
                                <p class="text-sm text-gray-500 mb-4">Enter the expiry date from the product packaging for
                                    each item:</p>
                                <table class="min-w-full bg-gray-50 rounded">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="p-3 text-left text-sm font-semibold text-gray-700">Product</th>
                                            <th class="p-3 text-center text-sm font-semibold text-gray-700">Qty</th>
                                            <th class="p-3 text-left text-sm font-semibold text-gray-700">Batch Number</th>
                                            <th class="p-3 text-left text-sm font-semibold text-gray-700">Expiry Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $index => $item)
                                            @if($item->quantity_received < $item->quantity_ordered)
                                                <tr class="border-b border-gray-200">
                                                    <td class="p-3 text-sm">{{ $item->product?->name ?? 'Unknown' }}</td>
                                                    <td class="p-3 text-center text-sm font-medium">
                                                        <div class="flex items-center justify-center">
                                                            <span class="text-xs text-gray-500 mr-2">/
                                                                {{ $item->quantity_ordered }}</span>
                                                            <input type="number" name="items[{{ $item->id }}][quantity]"
                                                                class="shadow appearance-none border rounded w-20 py-1 px-2 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline text-center"
                                                                required min="1"
                                                                max="{{ $item->quantity_ordered - $item->quantity_received }}"
                                                                value="{{ $item->quantity_ordered - $item->quantity_received }}">
                                                        </div>
                                                    </td>
                                                    <td class="p-3">
                                                        <input type="hidden" name="items[{{ $item->id }}][item_id]"
                                                            value="{{ $item->id }}">
                                                        <input type="text" name="items[{{ $item->id }}][batch_number]"
                                                            class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline"
                                                            required placeholder="e.g. LOT-12345">
                                                    </td>
                                                    <td class="p-3">
                                                        <input type="date" name="items[{{ $item->id }}][expiry_date]"
                                                            class="shadow appearance-none border rounded w-full py-1 px-2 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline"
                                                            required min="{{ now()->format('Y-m-d') }}">
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <button type="submit"
                                class="bg-green-600 hover:bg-green-800 text-white font-bold py-3 px-6 rounded w-full text-center"
                                onclick="return confirm('Confirm receipt of stock? This will update inventory levels.')">
                                Receive Stock & Update Inventory
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>