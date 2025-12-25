<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Receive New Stock') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('inventory.store') }}" method="POST">
                        @csrf

                        <!-- Product -->
                        <div class="mb-4">
                            <label for="product_id" class="block text-gray-700 text-sm font-bold mb-2">Product</label>
                            <select name="product_id" id="product_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                                <option value="">Select Product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Supplier -->
                        <div class="mb-4">
                            <label for="supplier_id" class="block text-gray-700 text-sm font-bold mb-2">Supplier</label>
                            <select name="supplier_id" id="supplier_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Select Supplier...</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Batch Number -->
                        <div class="mb-4">
                            <label for="batch_number" class="block text-gray-700 text-sm font-bold mb-2">Batch
                                Number</label>
                            <input type="text" name="batch_number" id="batch_number"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <!-- Quantity -->
                        <div class="mb-4">
                            <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity
                                Received</label>
                            <input type="number" name="quantity" id="quantity"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required min="1">
                        </div>

                        <!-- Expiry Date -->
                        <div class="mb-4">
                            <label for="expiry_date" class="block text-gray-700 text-sm font-bold mb-2">Expiry
                                Date</label>
                            <input type="date" name="expiry_date" id="expiry_date"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Add To Inventory
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>