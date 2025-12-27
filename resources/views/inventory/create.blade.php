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

                        <!-- Barcode Lookup -->
                        <div class="mb-4 bg-gray-100 p-3 rounded">
                            <label for="barcode_scan" class="block text-gray-700 text-sm font-bold mb-2">Scan Barcode
                                (Helper)</label>
                            <div class="flex gap-2">
                                <input type="text" id="barcode_scan"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    placeholder="Scan product barcode here to auto-select...">
                                <button type="button" onclick="startScanner()" class="bg-blue-600 text-white px-3 rounded hover:bg-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                    </svg>
                                </button>
                            </div>
                            <div id="scan-status" class="text-xs mt-1"></div>
                        </div>

                        <!-- Product -->
                        <div class="mb-4">
                            <label for="product_id" class="block text-gray-700 text-sm font-bold mb-2">Product</label>
                            <select name="product_id" id="product_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                                <option value="">Select Product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-barcode="{{ $product->barcode }}">
                                        {{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <script>
                            document.getElementById('barcode_scan').addEventListener('keydown', function (e) {
                                if (e.key === 'Enter') {
                                    e.preventDefault();
                                    const code = this.value.trim();
                                    const select = document.getElementById('product_id');
                                    let found = false;

                                    for (let i = 0; i < select.options.length; i++) {
                                        if (select.options[i].getAttribute('data-barcode') === code) {
                                            select.selectedIndex = i;
                                            found = true;
                                            document.getElementById('scan-status').innerHTML = '<span class="text-green-600 font-bold">Product Found: ' + select.options[i].text + '</span>';
                                            this.value = ''; // Clear
                                            break;
                                        }
                                    }

                                    if (!found) {
                                        document.getElementById('scan-status').innerHTML = '<span class="text-red-500">Product not found for barcode: ' + code + '</span>';
                                    }
                                }
                            });
                        </script>

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

                        <!-- Cost Price -->
                        <div class="mb-4">
                            <label for="cost_price" class="block text-gray-700 text-sm font-bold mb-2">Unit Cost
                                (GHS)</label>
                            <input type="number" step="0.01" name="cost_price" id="cost_price"
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

        <!-- Camera Scanner Modal (Reusable) -->
        <div id="scanner-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center">
            <div class="bg-white p-4 rounded-lg w-full max-w-md">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-bold">Scan Barcode</h3>
                    <button onclick="stopScanner()" class="text-red-500 font-bold">X</button>
                </div>
                <div id="reader" class="w-full"></div>
            </div>
        </div>
    </div>

    <!-- HTML5-QRCode Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        let html5QrcodeScanner = null;

        function startScanner() {
            document.getElementById('scanner-modal').classList.remove('hidden');
            
            if(html5QrcodeScanner) return;

            const onScanSuccess = (decodedText, decodedResult) => {
                const searchInput = document.getElementById('barcode_scan');
                searchInput.value = decodedText;
                
                const event = new KeyboardEvent('keydown', {
                    key: 'Enter',
                    code: 'Enter',
                    which: 13,
                    bubbles: true
                });
                searchInput.dispatchEvent(event);

                stopScanner();
            };

            html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: {width: 250, height: 250} },
                false);
            html5QrcodeScanner.render(onScanSuccess, (err) => {});
        }

        function stopScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().then(_ => {   
                    document.getElementById('scanner-modal').classList.add('hidden');
                    html5QrcodeScanner = null;
                }).catch(error => {
                    document.getElementById('scanner-modal').classList.add('hidden');
                });
            } else {
                document.getElementById('scanner-modal').classList.add('hidden');
            }
        }
    </script>
</x-app-layout>