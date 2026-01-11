<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            {{ __('Add New Product') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ productType: 'goods' }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Product Details</h3>
                    <p class="text-sm text-slate-500">Enter the information for the new item. Fields marked with * are
                        required.</p>
                </div>

                <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="col-span-1 md:col-span-2">
                            <x-form-input name="name" label="Product Name" placeholder="e.g. Moxclav 625mg" required />
                        </div>

                        <!-- Barcode Scanner -->
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Barcode / UPC</label>
                            <div class="flex gap-2">
                                <div class="flex-grow">
                                    <x-form-input name="barcode" id="barcode" placeholder="Scan or type barcode" />
                                </div>
                                <button type="button" @click="$dispatch('open-modal', 'scanner-modal'); startScanner()"
                                    class="inline-flex items-center justify-center px-4 py-2 border border-slate-300 shadow-sm text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75zM16.5 19.5h.75v.75h-.75v-.75z" />
                                    </svg>
                                    Scan
                                </button>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Press Enter after typing to auto-fetch details.</p>
                        </div>

                        <!-- Type & Category -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
                            <select name="product_type" x-model="productType"
                                class="block w-full rounded-md border-slate-300 py-2.5 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="goods">Goods (Physical Stock)</option>
                                <option value="service">Service (Consultation, etc.)</option>
                            </select>
                        </div>

                        @if(isset($branches) && $branches)
                            <!-- Branch Selector (Super Admin Only) -->
                            <div class="col-span-1 md:col-span-2 bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                                <label for="branch_id" class="block text-indigo-700 text-sm font-semibold mb-2">
                                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Assign to Branch
                                </label>
                                <select name="branch_id" id="branch_id"
                                    class="w-full border border-indigo-300 rounded-lg py-2.5 px-3 focus:ring-2 focus:ring-indigo-500">
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ $branch->is_main ? 'selected' : '' }}>
                                            {{ $branch->name }} {{ $branch->is_main ? '(Main)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-indigo-600 mt-1">As Super Admin, you can add products to any branch.
                                </p>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                            <div class="flex gap-2">
                                <select name="category_id" id="category_id"
                                    class="block w-full rounded-md border-slate-300 py-2.5 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select Category...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button"
                                    @click="$dispatch('open-modal', 'category-modal'); focusCategoryInput()"
                                    class="inline-flex items-center justify-center p-2 border border-transparent rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none ring-offset-2 focus:ring-2 focus:ring-green-500 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Price & Cost -->
                        <x-form-input type="number" step="0.01" name="unit_price" label="Selling Price (GHS)"
                            required />

                        <div>
                            <x-form-input type="number" step="0.01" name="cost_price" label="Cost Price (GHS)" value="0"
                                helper="Used for profit calculation." />
                        </div>
                    </div>

                    <!-- Goods Specific Details -->
                    <div x-show="productType === 'goods'" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0" class="pt-6 border-t border-slate-100">
                        <h4 class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wide">Pharmacy Details</h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <x-form-input name="dosage" label="Dosage" placeholder="e.g. 500mg" />

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Form</label>
                                <select name="drug_form"
                                    class="block w-full rounded-md border-slate-300 py-2.5 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select Form...</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Capsule">Capsule</option>
                                    <option value="Syrup">Syrup</option>
                                    <option value="Injection">Injection</option>
                                    <option value="Cream">Cream</option>
                                    <option value="Drops">Drops</option>
                                    <option value="Inhaler">Inhaler</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Route</label>
                                <select name="drug_route"
                                    class="block w-full rounded-md border-slate-300 py-2.5 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select Route...</option>
                                    <option value="Oral">Oral</option>
                                    <option value="Topical">Topical</option>
                                    <option value="Intravenous">Intravenous</option>
                                    <option value="Intramuscular">Intramuscular</option>
                                    <option value="Inhalation">Inhalation</option>
                                    <option value="Rectal">Rectal</option>
                                    <option value="Ophthalmic">Ophthalmic</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-form-input type="number" name="reorder_level" label="Reorder Level Alert" value="10"
                                required />

                            <div class="flex items-start pt-6">
                                <div class="flex items-center h-5">
                                    <input id="is_chronic" name="is_chronic" value="1" type="checkbox"
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_chronic" class="font-medium text-slate-700">Chronic
                                        Medication</label>
                                    <p class="text-slate-500">Enables refill reminders and supply days tracking.</p>
                                </div>
                            </div>

                            <div class="flex items-start pt-6">
                                <div class="flex items-center h-5">
                                    <input id="tax_exempt" name="tax_exempt" value="1" type="checkbox"
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="tax_exempt" class="font-medium text-slate-700">Tax Exempt</label>
                                    <p class="text-slate-500">Exclude this product from tax calculation in POS.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description"
                            class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                        <x-primary-button class="w-full sm:w-auto">
                            Save Product
                        </x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    <!-- Category Modal -->
    <x-modal name="category-modal" maxWidth="sm">
        <div class="p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Add New Category</h3>
            <p id="cat-error" class="text-red-600 text-sm mb-3 hidden bg-red-50 p-2 rounded"></p>

            <div class="mb-6">
                <x-form-input name="new_category_name" id="new_category_name" label="Category Name"
                    placeholder="e.g. Antibiotics" />
            </div>

            <div class="flex justify-end gap-3">
                <button @click="$dispatch('close-modal')"
                    class="px-4 py-2 border border-slate-300 rounded-md text-slate-700 text-sm font-medium hover:bg-slate-50 transition">
                    Cancel
                </button>
                <x-primary-button onclick="saveCategory()">
                    Save Category
                </x-primary-button>
            </div>
        </div>
    </x-modal>

    <!-- Scanner Modal -->
    <x-modal name="scanner-modal" maxWidth="md">
        <div class="p-4 relative">
            <button @click="stopScanner()" class="absolute top-2 right-2 text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
            <h3 class="text-lg font-bold text-slate-900 mb-2 text-center">Scan Barcode</h3>
            <div id="reader" class="w-full bg-slate-100 rounded-lg overflow-hidden h-64"></div>
            <p class="text-xs text-slate-500 mt-3 text-center">Position the barcode within the frame</p>
        </div>
    </x-modal>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        function focusCategoryInput() {
            setTimeout(() => {
                const input = document.getElementById('new_category_name');
                if (input) input.focus();
            }, 100);
            document.getElementById('cat-error').classList.add('hidden');
        }

        async function saveCategory() {
            const nameInput = document.getElementById('new_category_name');
            const name = nameInput.value.trim();
            if (!name) return;

            try {
                const res = await fetch('{{ route('categories.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ name: name })
                });

                const data = await res.json();

                if (data.success) {
                    const select = document.getElementById('category_id');
                    const option = new Option(data.category.name, data.category.id);
                    select.add(option, undefined);
                    select.value = data.category.id;
                    window.dispatchEvent(new CustomEvent('close-modal'));
                    nameInput.value = '';
                } else {
                    const err = document.getElementById('cat-error');
                    err.textContent = data.message || 'Error creating category';
                    err.classList.remove('hidden');
                }
            } catch (e) {
                console.error(e);
                const err = document.getElementById('cat-error');
                err.textContent = "Failed to save.";
                err.classList.remove('hidden');
            }
        }

        // --- Barcode Lookup Logic ---
        const barcodeInput = document.getElementById('barcode');
        if (barcodeInput) {
            barcodeInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const code = barcodeInput.value.trim();
                    if (code.length > 3) {
                        fetchMetadata(code);
                    }
                }
            });
        }

        async function fetchMetadata(barcode) {
            const nameInput = document.getElementById('name');
            const descInput = document.getElementById('description');
            const originalName = nameInput.value;

            nameInput.value = "Searching...";
            nameInput.disabled = true;

            try {
                const res = await fetch(`{{ route('products.lookup') }}?barcode=${barcode}`);
                const data = await res.json();

                if (data.success) {
                    nameInput.value = data.data.name;
                    descInput.value = data.data.description;
                } else {
                    nameInput.value = originalName;
                    alert('Product not found in global database. Please enter details manually.');
                }
            } catch (e) {
                console.error(e);
                nameInput.value = originalName;
                alert('Error fetching data.');
            } finally {
                nameInput.disabled = false;
            }
        }

        // --- Camera Scanner Logic ---
        let html5QrCode = null;

        function startScanner() {
            // Wait for modal transition
            setTimeout(() => {
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("reader");
                }

                const onScanSuccess = (decodedText, decodedResult) => {
                    document.getElementById('barcode').value = decodedText;
                    stopScanner();
                    fetchMetadata(decodedText);
                };

                const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                Html5Qrcode.getCameras().then(devices => {
                    if (devices && devices.length) {
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
                    console.error("Error getting cameras", err);
                    if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
                        alert("⚠️ Camera Access Error\n\nBrowsers block camera access on insecure (HTTP) connections.\n\nPlease:\n1. Use a USB Barcode Scanner.\n2. Or type the barcode manually.\n3. Or secure this site with HTTPS.");
                    } else {
                        alert("Camera permission error: " + err);
                    }
                    stopScanner();
                });
            }, 300);
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    window.dispatchEvent(new CustomEvent('close-modal'));
                }).catch((err) => {
                    console.warn("Failed to stop scanner", err);
                    window.dispatchEvent(new CustomEvent('close-modal'));
                });
            } else {
                window.dispatchEvent(new CustomEvent('close-modal'));
            }
        }
    </script>
</x-app-layout>