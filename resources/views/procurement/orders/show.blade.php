<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            PO #{{ $order->id }} - {{ $order->supplier->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p><strong>Status:</strong> <span class="uppercase font-bold">{{ $order->status }}</span>
                            </p>
                            <p><strong>Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
                            <p><strong>Expected:</strong>
                                {{ $order->expected_date ? $order->expected_date->format('d M Y') : 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold">{{ number_format($order->total_amount, 2) }}</p>
                            <p class="text-gray-500">Total Value</p>
                        </div>
                    </div>
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
                                    <td class="p-3">{{ $item->product->name }}</td>
                                    <td class="p-3 text-right">{{ $item->quantity_ordered }}</td>
                                    <td
                                        class="p-3 text-right font-bold {{ $item->quantity_received < $item->quantity_ordered ? 'text-red-500' : 'text-green-500' }}">
                                        {{ $item->quantity_received }}
                                    </td>
                                    <td class="p-3 text-right">{{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="p-3 text-right">
                                        {{ number_format($item->quantity_ordered * $item->unit_cost, 2) }}</td>
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