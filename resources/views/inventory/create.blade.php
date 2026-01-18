<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Receive New Stock') }}
            </h2>
            <a href="{{ route('inventory.history') }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded border border-gray-300">
                View History
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="stockReceiver()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('inventory.store') }}" method="POST" @submit="prepareSubmit">
                        @csrf

                        @if(isset($branches) && $branches)
                            <!-- Branch Selector (Super Admin Only) -->
                            <div class="mb-6 bg-indigo-50 p-3 rounded border border-indigo-200">
                                <label for="branch_id" class="block text-indigo-700 text-sm font-bold mb-2">
                                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Assign to Branch
                                </label>
                                <select name="branch_id" id="branch_id"
                                    class="shadow appearance-none border border-indigo-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ $branch->is_main ? 'selected' : '' }}>
                                            {{ $branch->name }} {{ $branch->is_main ? '(Main)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-indigo-600 mt-1">As Super Admin, you can receive stock to any branch.
                                </p>
                            </div>
                        @endif

                        <!-- Supplier (applies to all items) -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <label for="supplier_id" class="block text-gray-700 text-sm font-bold">Supplier
                                    (Optional)</label>
                                <a href="{{ route('suppliers.index') }}"
                                    class="text-sm text-blue-600 hover:text-blue-900">+ New Supplier</a>
                            </div>
                            <select name="supplier_id" id="supplier_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Select Supplier...</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="border-t pt-4 mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Stock Items</h3>

                            <!-- Barcode Quick Add -->
                            <div class="mb-4 bg-gray-100 p-3 rounded">
                                <label for="barcode_scan" class="block text-gray-700 text-sm font-bold mb-2">Quick Add
                                    (Scan/Enter Barcode)</label>
                                <div class="flex gap-2">
                                    <input type="text" id="barcode_scan" x-ref="barcodeInput"
                                        @keydown.enter.prevent="handleBarcodeScan"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        placeholder="Scan barcode to auto-add item...">
                                    <button type="button" @click="startScanner()"
                                        class="bg-blue-600 text-white px-3 rounded hover:bg-blue-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                        </svg>
                                    </button>
                                </div>
                                <div id="scan-status" class="text-xs mt-1"></div>
                            </div>

                            <!-- Items List -->
                            <div class="space-y-4">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="border rounded-lg p-4 bg-gray-50 relative">
                                        <button type="button" @click="removeItem(index)"
                                            class="absolute top-2 right-2 text-red-500 hover:text-red-700"
                                            x-show="items.length > 1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>

                                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                            <!-- Product -->
                                            <div class="md:col-span-2">
                                                <label class="block text-gray-700 text-xs font-bold mb-1">Product
                                                    *</label>
                                                <select x-model="item.product_id" required
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline">
                                                    <option value="">Select...</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}"
                                                            data-barcode="{{ $product->barcode }}">{{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Quantity -->
                                            <div>
                                                <label class="block text-gray-700 text-xs font-bold mb-1">Quantity
                                                    *</label>
                                                <input type="number" x-model="item.quantity" min="1" required
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline">
                                            </div>

                                            <!-- Batch Number -->
                                            <div>
                                                <label class="block text-gray-700 text-xs font-bold mb-1">Batch
                                                    #</label>
                                                <input type="text" x-model="item.batch_number"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline">
                                            </div>

                                            <!-- Cost Price -->
                                            <div>
                                                <label class="block text-gray-700 text-xs font-bold mb-1">Unit
                                                    Cost</label>
                                                <input type="number" x-model="item.cost_price" step="0.01" min="0"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline">
                                            </div>

                                            <!-- Expiry Date -->
                                            <div>
                                                <label class="block text-gray-700 text-xs font-bold mb-1">Expiry
                                                    Date</label>
                                                <input type="date" x-model="item.expiry_date"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Add Item Button -->
                            <button type="button" @click="addItem()"
                                class="mt-4 w-full border-2 border-dashed border-gray-300 rounded-lg py-3 text-gray-600 hover:border-blue-500 hover:text-blue-500 transition-colors">
                                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Another Item
                            </button>
                        </div>

                        <!-- Summary -->
                        <div class="bg-blue-50 rounded-lg p-4 mt-4" x-show="items.length > 0">
                            <div class="flex justify-between items-center">
                                <span class="text-blue-800 font-semibold">Total Items: <span
                                        x-text="items.filter(i => i.product_id).length"></span></span>
                                <span class="text-blue-800 font-semibold">Total Quantity: <span
                                        x-text="items.reduce((sum, i) => sum + (parseInt(i.quantity) || 0), 0)"></span></span>
                            </div>
                        </div>

                        <!-- Hidden Input for Items JSON -->
                        <input type="hidden" name="items" :value="JSON.stringify(items)">

                        <div class="flex items-center justify-between mt-6">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline"
                                :disabled="items.filter(i => i.product_id && i.quantity).length === 0">
                                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Receive Stock (<span
                                    x-text="items.filter(i => i.product_id && i.quantity).length"></span> items)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Camera Scanner Modal -->
    <div id="scanner-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-4 rounded-lg w-full max-w-md">
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-bold">Scan Barcode</h3>
                <button onclick="stopScanner()" class="text-red-500 font-bold">X</button>
            </div>
            <div id="reader" class="w-full"></div>
        </div>
    </div>

    <!-- HTML5-QRCode Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        // Product data for barcode lookup
        const productsData = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'barcode' => $p->barcode]));

        function stockReceiver() {
            return {
                items: [{ product_id: '', quantity: 1, batch_number: '', cost_price: '', expiry_date: '' }],

                addItem() {
                    this.items.push({ product_id: '', quantity: 1, batch_number: '', cost_price: '', expiry_date: '' });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                handleBarcodeScan() {
                    const code = this.$refs.barcodeInput.value.trim();
                    if (!code) return;

                    const statusEl = document.getElementById('scan-status');
                    statusEl.innerHTML = '<span class="text-gray-500">Processing...</span>';

                    // Parse GS1 if applicable
                    const parsed = parseGS1(code);
                    const searchCode = parsed.gtin || code;

                    // Find product
                    const normalize = (s) => s.replace(/^0+/, '');
                    const normSearch = normalize(searchCode);

                    let foundProduct = null;
                    for (let p of productsData) {
                        if (!p.barcode) continue;
                        const normBarcode = normalize(p.barcode);

                        if (p.barcode === searchCode || normBarcode === normSearch) {
                            foundProduct = p;
                            break;
                        }
                        // Suffix match for GTIN-14 containing EAN-13
                        if (normSearch.length > normBarcode.length && normSearch.endsWith(normBarcode) && normBarcode.length >= 8) {
                            foundProduct = p;
                            break;
                        }
                    }

                    if (foundProduct) {
                        // Check if already added
                        const existing = this.items.find(i => i.product_id == foundProduct.id);
                        if (existing) {
                            existing.quantity = (parseInt(existing.quantity) || 0) + 1;
                            if (parsed.batch && !existing.batch_number) existing.batch_number = parsed.batch;
                            if (parsed.expiry && !existing.expiry_date) existing.expiry_date = parsed.expiry;
                            statusEl.innerHTML = `<span class="text-blue-600">Quantity increased for: ${foundProduct.name}</span>`;
                        } else {
                            // Add new or use empty slot
                            const emptySlot = this.items.find(i => !i.product_id);
                            const newItem = {
                                product_id: foundProduct.id,
                                quantity: 1,
                                batch_number: parsed.batch || '',
                                cost_price: '',
                                expiry_date: parsed.expiry || ''
                            };

                            if (emptySlot) {
                                Object.assign(emptySlot, newItem);
                            } else {
                                this.items.push(newItem);
                            }
                            statusEl.innerHTML = `<span class="text-green-600 font-bold">Added: ${foundProduct.name}</span>`;
                        }
                    } else {
                        statusEl.innerHTML = `<span class="text-red-500">Product not found for barcode: ${searchCode}</span>`;
                    }

                    this.$refs.barcodeInput.value = '';
                    this.$refs.barcodeInput.focus();
                },

                prepareSubmit(e) {
                    // Remove items without product_id
                    const validItems = this.items.filter(i => i.product_id && i.quantity);
                    if (validItems.length === 0) {
                        e.preventDefault();
                        alert('Please add at least one product with quantity.');
                        return false;
                    }
                    // Update hidden input
                    document.querySelector('input[name="items"]').value = JSON.stringify(validItems);
                    return true;
                }
            }
        }

        // GS1 Parsing Functions
        function parseGS1(code) {
            let result = { gtin: null, batch: null, expiry: null };
            let temp = code;

            if (code.includes('(') && code.includes(')')) {
                const ai01 = code.match(/\(01\)(\d{14})/);
                if (ai01) result.gtin = ai01[1];
                const ai17 = code.match(/\(17\)(\d{6,8})/);
                if (ai17) result.expiry = formatGS1Date(ai17[1]);
                const ai10 = code.match(/\(10\)([^\(\)]+)/);
                if (ai10) result.batch = ai10[1];
                return result;
            }

            const knownAIs = ['01', '10', '11', '15', '17', '21', '30'];
            let loops = 0;
            while (temp.length > 0 && loops < 20) {
                loops++;
                if (temp.startsWith('01') && temp.length >= 16) {
                    result.gtin = temp.substring(2, 16);
                    temp = temp.substring(16);
                } else if (temp.startsWith('17') && temp.length >= 8) {
                    result.expiry = formatGS1Date(temp.substring(2, 8));
                    temp = temp.substring(8);
                } else if (temp.startsWith('10')) {
                    result.batch = temp.substring(2);
                    temp = "";
                } else {
                    break;
                }
            }
            return result;
        }

        function formatGS1Date(dateStr) {
            if (!dateStr) return null;
            if (dateStr.length === 8) {
                return `${dateStr.substring(0, 4)}-${dateStr.substring(4, 6)}-${dateStr.substring(6, 8)}`;
            }
            if (dateStr.length === 6) {
                let yy = parseInt(dateStr.substring(0, 2));
                let year = (yy >= 50) ? (1900 + yy) : (2000 + yy);
                let dd = dateStr.substring(4, 6);
                if (dd === '00') dd = '01';
                return `${year}-${dateStr.substring(2, 4)}-${dd}`;
            }
            return null;
        }

        // Camera Scanner
        let html5QrCode = null;

        function startScanner() {
            document.getElementById('scanner-modal').classList.remove('hidden');
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            const onScanSuccess = (decodedText) => {
                const input = document.getElementById('barcode_scan');
                input.value = decodedText;
                input.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', code: 'Enter', which: 13, bubbles: true }));
                stopScanner();
            };

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    let cameraId = devices[0].id;
                    for (let d of devices) {
                        if (d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('environment')) {
                            cameraId = d.id;
                            break;
                        }
                    }
                    html5QrCode.start(cameraId, { fps: 10, qrbox: { width: 250, height: 250 } }, onScanSuccess);
                }
            });
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().finally(() => {
                    document.getElementById('scanner-modal').classList.add('hidden');
                });
            } else {
                document.getElementById('scanner-modal').classList.add('hidden');
            }
        }
    </script>
</x-app-layout>