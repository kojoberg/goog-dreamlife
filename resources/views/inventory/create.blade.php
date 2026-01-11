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
                                <button type="button" onclick="startScanner()"
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

                        <!-- Product -->
                        <div class="mb-4">
                            <label for="product_id" class="block text-gray-700 text-sm font-bold mb-2">Product</label>
                            <select name="product_id" id="product_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                                <option value="">Select Product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-barcode="{{ $product->barcode }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if(isset($branches) && $branches)
                            <!-- Branch Selector (Super Admin Only) -->
                            <div class="mb-4 bg-indigo-50 p-3 rounded border border-indigo-200">
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


                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <label for="supplier_id" class="block text-gray-700 text-sm font-bold">Supplier</label>
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
        // --- Barcode Selection & GS1 Parsing Logic ---
        document.getElementById('barcode_scan').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const code = this.value.trim();
                if (!code) return;

                document.getElementById('scan-status').innerHTML = '<span class="text-gray-500">Processing...</span>';

                // Attempt GS1 Parse
                const p = parseGS1(code);
                console.log("GS1 Parse Result:", p);

                // Display Debug Info (Temporary for verification)
                let debugMsg = `<div class="text-[10px] text-gray-500 mt-1 bg-gray-50 p-1 rounded border">
                    Parsed: GTIN=${p.gtin || 'N/A'}, Batch=${p.batch || 'N/A'}, Exp=${p.expiry || 'N/A'}
                </div>`;

                // 1. Fill Fields
                if (p.batch) document.getElementById('batch_number').value = p.batch;
                if (p.expiry) document.getElementById('expiry_date').value = p.expiry;

                // 2. Find Product
                const searchCode = p.gtin || code;
                const select = document.getElementById('product_id');
                let found = false;

                const normalize = (s) => s.replace(/^0+/, '');
                const normSearch = normalize(searchCode);

                for (let i = 0; i < select.options.length; i++) {
                    const optBarcode = select.options[i].getAttribute('data-barcode');
                    if (!optBarcode) continue;

                    const normOpt = normalize(optBarcode);

                    // Match Logic: Exact, Suffix (GTIN-14 containing EAN-13), or Prefix (rare)
                    let match = false;
                    if (optBarcode === searchCode || normOpt === normSearch) match = true;
                    // Suffix Match: Search "1890..." vs Opt "890..."
                    else if (normSearch.length > normOpt.length && normSearch.endsWith(normOpt) && normOpt.length >= 8) match = true;
                    // Reverse Suffix (unlikely but safe)
                    else if (normOpt.length > normSearch.length && normOpt.endsWith(normSearch) && normSearch.length >= 8) match = true;

                    if (match) {
                        select.selectedIndex = i;
                        found = true;

                        let msg = `<span class="text-green-600 font-bold">Product Found: ${select.options[i].text}</span>`;
                        if (p.batch || p.expiry) msg += ' <span class="text-blue-600 text-xs">(GS1 Data Filled)</span>';
                        msg += debugMsg;

                        document.getElementById('scan-status').innerHTML = msg;
                        this.value = '';
                        break;
                    }
                }

                if (!found) {
                    document.getElementById('scan-status').innerHTML = `<span class="text-red-500 font-bold">Product not found.</span> ${debugMsg}`;
                }
            }
        });

        function parseGS1(code) {
            let result = { gtin: null, batch: null, expiry: null };
            let temp = code;

            // Bracketed Handler
            if (code.includes('(') && code.includes(')')) {
                const ai01 = code.match(/\(01\)(\d{14})/);
                if (ai01) result.gtin = ai01[1];

                const ai17 = code.match(/\(17\)(\d{6,8})/);
                if (ai17) result.expiry = formatGS1Date(ai17[1]);

                const ai11 = code.match(/\(11\)(\d{6,8})/);

                const ai10 = code.match(/\(10\)([^\(\)]+)/);
                if (ai10) result.batch = ai10[1];

                return result;
            }

            // Raw Loop with Lookahead
            const knownAIs = ['01', '10', '11', '15', '17', '21', '30'];
            const startsWithAI = (s) => knownAIs.some(a => s.startsWith(a));

            let loops = 0;
            while (temp.length > 0 && loops < 20) {
                loops++;

                if (temp.startsWith('01') && temp.length >= 16) {
                    result.gtin = temp.substring(2, 16);
                    temp = temp.substring(16);
                }
                else if (temp.startsWith('11') || temp.startsWith('17') || temp.startsWith('15')) {
                    // Dates: 11 (Prod), 17 (Exp), 15 (BestBefore)
                    let ai = temp.substring(0, 2);
                    let rest = temp.substring(2);

                    let cand6 = rest.substring(0, 6);
                    let cand8 = rest.substring(0, 8);

                    let valid6 = isValidYYMMDD(cand6) || isValidYYYYMM(cand6);
                    let valid8 = (rest.length >= 8) && isValidYYYYMMDD(cand8);

                    let take8 = false;

                    if (valid6 && valid8) {
                        let follow6 = rest.substring(6);
                        let follow8 = rest.substring(8);

                        let aiAfter6 = startsWithAI(follow6);
                        let aiAfter8 = startsWithAI(follow8);

                        if (aiAfter6 && !aiAfter8) take8 = false;
                        else if (!aiAfter6 && aiAfter8) take8 = true;
                        else take8 = false; // Default to 6
                    }
                    else if (valid8) take8 = true;
                    else take8 = false;

                    let len = take8 ? 8 : 6;
                    let val = rest.substring(0, len);
                    let isYm = isValidYYYYMM(val) && !isValidYYMMDD(val);

                    if (ai === '17') result.expiry = formatGS1Date(val, isYm);

                    temp = temp.substring(2 + len);
                }
                else if (temp.startsWith('10')) {
                    result.batch = temp.substring(2);
                    temp = "";
                }
                else if (temp.startsWith('21')) {
                    temp = "";
                }
                else {
                    break;
                }
            }

            return result;
        }

        function isValidYYYYMM(d) {
            if (!d || d.length !== 6) return false;
            // Heuristic: Ends in 01-12, Starts with 19 or 20
            if (!d.startsWith('19') && !d.startsWith('20')) return false;
            let mm = parseInt(d.substring(4, 6));
            if (mm < 1 || mm > 12) return false;
            return true;
        }

        function isValidYYMMDD(d) {
            if (!d || d.length !== 6) return false;
            let mm = parseInt(d.substring(2, 4));
            let dd = parseInt(d.substring(4, 6));
            if (mm < 1 || mm > 12) return false;
            if (dd < 0 || dd > 31) return false; // 00 allowed sometimes
            return true;
        }

        function isValidYYYYMMDD(d) {
            if (!d || d.length !== 8) return false;
            let mm = parseInt(d.substring(4, 6));
            let dd = parseInt(d.substring(6, 8));
            if (mm < 1 || mm > 12) return false;
            if (dd < 0 || dd > 31) return false;
            return true;
        }

        function formatGS1Date(dateStr, isYYYYMM = false) {
            if (!dateStr) return null;

            // YYYYMMDD
            if (dateStr.length === 8) {
                let yyyy = dateStr.substring(0, 4);
                let mm = dateStr.substring(4, 6);
                let dd = dateStr.substring(6, 8);
                if (dd === '00') dd = '01';
                return `${yyyy}-${mm}-${dd}`;
            }

            if (isYYYYMM) {
                let yyyy = dateStr.substring(0, 4);
                let mm = dateStr.substring(4, 6);
                let dd = '01';
                return `${yyyy}-${mm}-${dd}`;
            }

            // YYMMDD
            if (dateStr.length === 6) {
                let yy = parseInt(dateStr.substring(0, 2));
                let mm = dateStr.substring(2, 4);
                let dd = dateStr.substring(4, 6);
                let year = (yy >= 50) ? (1900 + yy) : (2000 + yy);
                if (dd === '00') dd = '01';
                return `${year}-${mm}-${dd}`;
            }

            return null;
        }

        // --- Camera Scanner Logic ---
        let html5QrCode = null;

        function startScanner() {
            document.getElementById('scanner-modal').classList.remove('hidden');

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            const onScanSuccess = (decodedText, decodedResult) => {
                console.log(`Code matched = ${decodedText}`, decodedResult);

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

            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    // Try to find back camera
                    let cameraId = devices[0].id;
                    for (let d of devices) {
                        if (d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('environment')) {
                            cameraId = d.id;
                            break;
                        }
                    }

                    html5QrCode.start(
                        cameraId,
                        config,
                        onScanSuccess
                    ).catch(err => {
                        // retry with any camera
                        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
                    });
                } else {
                    alert("No cameras found.");
                    stopScanner();
                }
            }).catch(err => {
                alert("Camera permission error: " + err);
                stopScanner();
            });
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then((ignore) => {
                    document.getElementById('scanner-modal').classList.add('hidden');
                }).catch((err) => {
                    document.getElementById('scanner-modal').classList.add('hidden');
                });
            } else {
                document.getElementById('scanner-modal').classList.add('hidden');
            }
        }
    </script>
</x-app-layout>