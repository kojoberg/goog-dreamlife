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
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Order Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Supplier</label>
                                <div class="flex gap-2">
                                    <select name="supplier_id" id="supplier_id"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-white">
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" onclick="openSupplierModal()"
                                        class="bg-green-600 text-white px-3 py-2 rounded shadow hover:bg-green-700 transition"
                                        title="Add New Supplier">
                                        +
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Expected Date</label>
                                <input type="date" name="expected_date"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-lg font-bold">Order Items</h3>
                            <button type="button" @click="addItem()"
                                class="bg-indigo-100 hover:bg-indigo-200 text-indigo-800 font-bold py-1 px-3 rounded text-sm">
                                + Add Item
                            </button>
                        </div>

                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr class="bg-gray-50 border-b">
                                        <th
                                            class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2">
                                            Product</th>
                                        <th
                                            class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                            Quantity</th>
                                        <th
                                            class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                            Unit Cost</th>
                                        <th
                                            class="p-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">
                                            Total</th>
                                        <th class="p-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr class="hover:bg-gray-50">
                                            <td class="p-2">
                                                <select :name="`items[${index}][product_id]`" x-model="item.product_id"
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                    <option value="">Select Product...</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="p-2">
                                                <input type="number" :name="`items[${index}][quantity]`"
                                                    x-model="item.quantity"
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                    min="1">
                                            </td>
                                            <td class="p-2">
                                                <input type="number" step="0.01" :name="`items[${index}][unit_cost]`"
                                                    x-model="item.unit_cost"
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            </td>
                                            <td class="p-2 font-bold text-gray-700">
                                                <span x-text="(item.quantity * item.unit_cost).toFixed(2)"></span>
                                            </td>
                                            <td class="p-2 text-center">
                                                <button type="button" @click="removeItem(index)"
                                                    class="text-red-600 hover:text-red-900 font-bold px-2">&times;</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                            <textarea name="notes" rows="3"
                                placeholder="Enter any specific instructions or notes for this order..."
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                                Create Purchase Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('purchaseOrder', () => ({
                items: [
                    { product_id: '', quantity: 1, unit_cost: 0 }
                ],
                addItem() {
                    this.items.push({ product_id: '', quantity: 1, unit_cost: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                }
            }))
        })
    </script>
</x-app-layout>

<!-- Supplier Modal -->
<div id="supplier-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-full max-w-md">
        <h3 class="font-bold text-lg mb-4">Add New Supplier</h3>
        <p id="supplier-error" class="text-red-500 text-sm mb-2 hidden"></p>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Supplier Name *</label>
            <input type="text" id="new_supplier_name"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Contact Person</label>
            <input type="text" id="new_contact_person"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Phone</label>
            <input type="text" id="new_supplier_phone"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input type="email" id="new_supplier_email"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="flex justify-end gap-2">
            <button onclick="closeSupplierModal()"
                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
            <button onclick="saveSupplier()"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
        </div>
    </div>
</div>

<script>
    function openSupplierModal() {
        document.getElementById('supplier-modal').classList.remove('hidden');
        document.getElementById('new_supplier_name').value = '';
        document.getElementById('new_contact_person').value = '';
        document.getElementById('new_supplier_phone').value = '';
        document.getElementById('new_supplier_email').value = '';
        setTimeout(() => document.getElementById('new_supplier_name').focus(), 100);
        document.getElementById('supplier-error').classList.add('hidden');
    }

    function closeSupplierModal() {
        document.getElementById('supplier-modal').classList.add('hidden');
    }

    async function saveSupplier() {
        const name = document.getElementById('new_supplier_name').value.trim();
        const contact_person = document.getElementById('new_contact_person').value.trim();
        const phone = document.getElementById('new_supplier_phone').value.trim();
        const email = document.getElementById('new_supplier_email').value.trim();

        if (!name) {
            document.getElementById('supplier-error').textContent = "Name is required.";
            document.getElementById('supplier-error').classList.remove('hidden');
            return;
        }

        try {
            const res = await fetch('{{ route('suppliers.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name, contact_person, phone, email })
            });

            const data = await res.json();

            if (data.success) {
                // Add to select
                const select = document.getElementById('supplier_id');
                const option = new Option(data.supplier.name, data.supplier.id);
                select.add(option, undefined);
                select.value = data.supplier.id;
                closeSupplierModal();
            } else {
                document.getElementById('supplier-error').textContent = data.message || 'Error creating supplier';
                document.getElementById('supplier-error').classList.remove('hidden');
            }
        } catch (e) {
            console.error(e);
            document.getElementById('supplier-error').textContent = "Failed to save.";
            document.getElementById('supplier-error').classList.remove('hidden');
        }
    }
</script>