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
                            <div class="flex gap-2">
                                <input type="text" name="barcode" id="barcode" value="{{ $product->barcode }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    placeholder="Scan or type barcode key">
                                <button type="button" onclick="startScanner()"
                                    class="bg-blue-600 text-white px-3 py-2 rounded shadow hover:bg-blue-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75zM16.5 19.5h.75v.75h-.75v-.75z" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Press Enter after typing to auto-fetch details from
                                OpenFoodFacts.</p>
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

<!-- Scanner Modal -->
<div id="scanner-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center">
    <div class="bg-white p-4 rounded-lg w-full max-w-md">
        <div class="flex justify-between items-center mb-2">
            <h3 class="font-bold">Scan Barcode</h3>
            <button onclick="stopScanner()" class="text-red-500 font-bold">X</button>
        </div>
        <div id="reader" class="w-full"></div>
        <p class="text-xs text-gray-500 mt-2 text-center">Point camera at barcode</p>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    // --- Barcode Lookup Logic ---
    const barcodeInput = document.getElementById('barcode');

    barcodeInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Prevent form submit
            const code = barcodeInput.value.trim();
            if (code.length > 3) {
                fetchMetadata(code);
            }
        }
    });

    async function fetchMetadata(barcode) {
        const nameInput = document.getElementById('name');
        const descInput = document.getElementById('description');
        const originalName = nameInput.value;
        const originalDesc = descInput.value;
        
        // Don't overwrite existing values immediately if just adding barcode
        // But the user might want to refresh details...
        // Let's ask via confirm if name is not empty
        if (nameInput.value && !confirm('Fetch and overwrite product details from OpenFoodFacts?')) {
            return;
        }

        nameInput.value = "Searching...";
        nameInput.disabled = true;

        try {
            const res = await fetch(`{{ route('products.lookup') }}?barcode=${barcode}`);
            const data = await res.json();

            if (data.success) {
                nameInput.value = data.data.name;
                descInput.value = data.data.description;
            } else {
                nameInput.value = originalName; // Revert
                if(!originalName) nameInput.value = '';
                alert('Product not found in global database. You can still save this barcode.');
            }
        } catch (e) {
             console.error(e);
             nameInput.value = originalName;
             if(!originalName) nameInput.value = '';
             alert('Error fetching data.');
        } finally {
            nameInput.disabled = false;
        }
    }

    // --- Camera Scanner Logic (Reused) ---
    let html5QrCode = null;

    function startScanner() {
        document.getElementById('scanner-modal').classList.remove('hidden');

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        const onScanSuccess = (decodedText, decodedResult) => {
            console.log(`Code matched = ${decodedText}`, decodedResult);

            document.getElementById('barcode').value = decodedText;
            stopScanner();

            // Auto fetch
            fetchMetadata(decodedText);
        };

        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                const cameraId = devices[0].id;
                html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    onScanSuccess
                ).catch(err => {
                    alert("Error starting camera: " + err);
                    stopScanner();
                });
            } else {
                alert("No cameras found.");
                stopScanner();
            }
        }).catch(err => {
            alert("Camera permission denied or error: " + err);
            stopScanner();
        });
    }

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then((ignore) => {
                document.getElementById('scanner-modal').classList.add('hidden');
            }).catch((err) => {
                console.warn("Failed to stop scanner", err);
                document.getElementById('scanner-modal').classList.add('hidden');
            });
        } else {
            document.getElementById('scanner-modal').classList.add('hidden');
        }
    }
</script>