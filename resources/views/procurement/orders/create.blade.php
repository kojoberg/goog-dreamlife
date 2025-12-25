<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Purchase Order') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="purchaseOrder()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('procurement.orders.store') }}" method="POST">
                        @csrf

                        <!-- Header -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Supplier</label>
                                <select name="supplier_id"
                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Expected Date</label>
                                <input type="date" name="expected_date"
                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <h3 class="text-lg font-bold mb-2">Order Items</h3>
                        <table class="w-full mb-4">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="p-2 text-left">Product</th>
                                    <th class="p-2 text-left">Quantity</th>
                                    <th class="p-2 text-left">Unit Cost</th>
                                    <th class="p-2 text-left">Total</th>
                                    <th class="p-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr>
                                        <td class="p-2">
                                            <select :name="`items[${index}][product_id]`" x-model="item.product_id"
                                                class="w-full border rounded p-1">
                                                <option value="">Select Product...</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" :name="`items[${index}][quantity]`"
                                                x-model="item.quantity" class="w-full border rounded p-1" min="1">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" step="0.01" :name="`items[${index}][unit_cost]`"
                                                x-model="item.unit_cost" class="w-full border rounded p-1">
                                        </td>
                                        <td class="p-2 font-bold">
                                            <span x-text="(item.quantity * item.unit_cost).toFixed(2)"></span>
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" @click="removeItem(index)"
                                                class="text-red-600 font-bold">&times;</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <button type="button" @click="addItem()"
                            class="mb-6 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-3 rounded">
                            + Add Item
                        </button>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                            <textarea name="notes"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>

                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Create Purchase Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function purchaseOrder() {
            return {
                items: [
                    { product_id: '', quantity: 1, unit_cost: 0 }
                ],
                addItem() {
                    this.items.push({ product_id: '', quantity: 1, unit_cost: 0 });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>