<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('products.update', $product) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Product Name</label>
                            <input type="text" name="name" id="name" value="{{ $product->name }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                        </div>

                        <!-- Barcode (New) -->
                        <div class="mb-4">
                            <label for="barcode" class="block text-gray-700 text-sm font-bold mb-2">Barcode /
                                UPC</label>
                            <input type="text" name="barcode" id="barcode" value="{{ $product->barcode }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Scan or type barcode key">
                        </div>

                        <!-- Product Type -->
                        <div class="mb-4">
                            <label for="product_type" class="block text-gray-700 text-sm font-bold mb-2">Type</label>
                            <select name="product_type" id="product_type"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="goods" {{ $product->product_type == 'goods' ? 'selected' : '' }}>Goods
                                    (Physical Stock)</option>
                                <option value="service" {{ $product->product_type == 'service' ? 'selected' : '' }}>
                                    Service (Consultation, BP Check, etc.)</option>
                            </select>
                        </div>

                        <!-- Price and Cost Price -->
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label for="unit_price" class="block text-gray-700 text-sm font-bold mb-2">Selling Price
                                    (GHS)</label>
                                <input type="number" step="0.01" name="unit_price" id="unit_price"
                                    value="{{ $product->unit_price }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required>
                            </div>
                            <div>
                                <label for="cost_price" class="block text-gray-700 text-sm font-bold mb-2">Cost Price
                                    (GHS)</label>
                                <input type="number" step="0.01" name="cost_price" id="cost_price"
                                    value="{{ $product->cost_price }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <p class="text-xs text-gray-500 mt-1">Used for profit calculation.</p>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                            <select name="category_id" id="category_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reorder Level -->
                        <div class="mb-4">
                            <label for="reorder_level" class="block text-gray-700 text-sm font-bold mb-2">Reorder Level
                                Alert</label>
                            <input type="number" name="reorder_level" id="reorder_level"
                                value="{{ $product->reorder_level }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_chronic" value="1" {{ $product->is_chronic ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-blue-600">
                                <span class="ml-2 text-gray-700 font-bold">Chronic Medication (Refill Reminders)</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 ml-7">If checked, POS will prompt for "Days Supply" and
                                system will schedule SMS reminders.</p>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description"
                                class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                            <textarea name="description" id="description"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ $product->description }}</textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>