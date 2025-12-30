<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Point of Sale') }}
        </h2>
    </x-slot>

    <div class="py-12" id="pos-app">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Product Grid (Left) -->
                <div class="lg:w-2/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex gap-2 mb-4">
                            <input type="text" id="search" placeholder="Search products or scan barcode..."
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onkeyup="filterProducts()" onkeydown="handleBarcodeScan(event)">
                            <button onclick="startScanner()"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 overflow-y-auto max-h-[600px]"
                            id="product-grid">
                            @foreach ($products as $product)
                                <div class="product-card bg-gray-50 border rounded-lg p-4 cursor-pointer hover:bg-blue-50 transition"
                                    onclick="addToCart({{ json_encode($product) }})"
                                    data-name="{{ strtolower($product['name']) }}"
                                    data-barcode="{{ $product['barcode'] ?? '' }}">
                                    <div class="font-bold text-gray-800">{{ $product['name'] }}</div>
                                    <div class="text-xs text-gray-600 mb-1">
                                        @if($product['dosage']) {{ $product['dosage'] }} @endif
                                        @if($product['form']) {{ $product['form'] }} @endif
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $product['category'] }}</div>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="font-bold text-blue-600">GHS
                                            {{ number_format($product['price'], 2) }}</span>
                                        @if($product['type'] !== 'service')
                                            <span class="text-xs bg-gray-200 px-2 py-1 rounded">Qty:
                                                {{ $product['stock'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Cart (Right) -->
                <div class="lg:w-1/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 h-full flex flex-col">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold">Current Order</h3>
                            <div class="flex gap-2">
                                <!-- Hold Cart -->
                                <button onclick="holdCart()" title="Hold Cart"
                                    class="text-yellow-600 hover:bg-yellow-100 p-1 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 00-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.959.401v0c.31 0 .555-.26.532-.57a48.039 48.039 0 01-.642-5.056c-1.518-.19-3.057-.309-4.616-.354a.64.64 0 00-.657.643v0z" />
                                    </svg>
                                </button>

                                <!-- Restore Cart (With Badge) -->
                                <button onclick="showHeldCarts()" title="Recall Cart"
                                    class="text-blue-600 hover:bg-blue-100 p-1 rounded relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                    </svg>
                                    <span id="held-count"
                                        class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full px-1 hidden">0</span>
                                </button>

                                <!-- Clear Cart -->
                                <button onclick="clearCart()" title="Clear Cart"
                                    class="text-red-600 hover:bg-red-100 p-1 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Held Carts Modal -->
                        <div id="held-carts-modal"
                            class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center">
                            <div class="bg-white p-4 rounded-lg w-full max-w-md">
                                <div class="flex justify-between items-center mb-2">
                                    <h3 class="font-bold">Held Carts</h3>
                                    <button
                                        onclick="document.getElementById('held-carts-modal').classList.add('hidden')"
                                        class="text-red-500 font-bold">X</button>
                                </div>
                                <div id="held-carts-list" class="space-y-2 max-h-64 overflow-y-auto"></div>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto mb-4" id="cart-items">
                            <!-- Cart items injected here -->
                            <div class="text-center text-gray-400 mt-10" id="empty-cart-msg">Cart is empty</div>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between mb-2">
                                <span>Total</span>
                                <span class="font-bold text-xl" id="cart-total">GHS 0.00</span>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-1">Payment Method</label>
                                <select id="payment-method" class="w-full border rounded p-2">
                                    <option value="cash">Cash</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="card">Card</option>
                                </select>
                            </div>

                            <!-- Customer Section -->
                            <div class="mb-4 border-b pb-4">
                                <label class="block text-sm font-bold mb-1">Customer / Patient</label>

                                <!-- Search Input -->
                                <div class="flex gap-2 mb-2">
                                    <input type="text" id="patient-search" placeholder="Search Name or Phone..."
                                        class="w-full border rounded p-2 text-sm" onkeyup="debounceSearchPatient()">
                                    <button onclick="openQuickRegister()"
                                        class="bg-green-500 text-white px-3 rounded text-sm">+</button>
                                </div>

                                <!-- Search Results -->
                                <div id="patient-results"
                                    class="bg-white border rounded shadow-lg absolute z-10 w-64 hidden max-h-48 overflow-y-auto">
                                </div>

                                <!-- Selected Patient -->
                                <div id="selected-patient-info"
                                    class="hidden bg-blue-50 p-2 rounded border border-blue-200">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-bold text-sm" id="sp-name"></div>
                                            <div class="text-xs text-gray-500">Points: <span id="sp-points"
                                                    class="font-bold text-blue-600"></span></div>
                                        </div>
                                        <button onclick="detachPatient()" class="text-red-500 text-xs">x</button>
                                    </div>

                                    <!-- Redeem Option -->
                                    <div class="mt-2 text-xs" id="redeem-section">
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" id="redeem-check" onchange="toggleRedemption()">
                                            <span>Redeem Points</span>
                                        </label>
                                        <div id="redeem-input-div" class="hidden mt-1">
                                            <input type="number" id="redeem-amount" class="w-20 border rounded px-1"
                                                placeholder="Pts" oninput="calculateRedemption()">
                                            <span class="text-gray-500 ml-1">Value: GHS <span
                                                    id="redeem-value">0.00</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-1">Customer Phone (Optional for SMS)</label>
                                <input type="text" id="customer-phone" placeholder="0244123456"
                                    class="w-full border rounded p-2">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-1">Customer Email (Optional)</label>
                                <input type="email" id="customer-email" placeholder="receipt@example.com"
                                    class="w-full border rounded p-2">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-1">Amount Tendered</label>
                                <input type="number" step="0.01" id="amount-tendered" class="w-full border rounded p-2"
                                    oninput="calculateChange()">
                            </div>

                            <div class="flex justify-between mb-4 text-green-600 font-bold" id="change-display"
                                style="display:none">
                                <span>Change:</span>
                                <span id="change-amount">GHS 0.00</span>
                            </div>

                            <button onclick="checkout()"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition disabled:opacity-50"
                                id="checkout-btn" disabled>
                                Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Camera Scanner Modal (Hidden by default) -->
    <div id="scanner-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center">
        <div class="bg-white p-4 rounded-lg w-full max-w-md">
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-bold">Scan Barcode</h3>
                <button onclick="stopScanner()" class="text-red-500 font-bold">X</button>
            </div>
            <div id="reader" class="w-full"></div>
            <p class="text-xs text-gray-500 mt-2 text-center">Point camera at barcode/QR code</p>
        </div>
    </div>
    </div>

    <!-- HTML5-QRCode Library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        // Scanner Logic
        let html5QrCode = null;

        function startScanner() {
            document.getElementById('scanner-modal').classList.remove('hidden');

            if (html5QrCode) {
                // If instance exists but not running, we might need to restart?
                // For simplicity, let's just proceed to start.
            } else {
                html5QrCode = new Html5Qrcode("reader");
            }

            const onScanSuccess = (decodedText, decodedResult) => {
                console.log(`Code matched = ${decodedText}`, decodedResult);

                const searchInput = document.getElementById('search');
                searchInput.value = decodedText;

                // Trigger Enter
                const event = new KeyboardEvent('keydown', {
                    key: 'Enter', code: 'Enter', which: 13, bubbles: true
                });
                searchInput.dispatchEvent(event);

                stopScanner();
            };

            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            // Request permission explicitly by listing cameras first
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    // Use the first "back" camera or just the first available
                    // Generally the last one in list is the back camera on mobile
                    const cameraId = devices[0].id;

                    html5QrCode.start(
                        { facingMode: "environment" }, // Prefer back camera
                        config,
                        onScanSuccess
                    ).catch(err => {
                        console.error("Error starting scanner", err);
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
                    alert("⚠️ Camera Access Error\n\nBrowsers block camera access on insecure (HTTP) connections.\n\nPlease:\n1. Use a USB Barcode Scanner (works without camera).\n2. Or type the barcode manually in the search box.\n3. Or secure this site with HTTPS (SSL Certificate).");
                } else {
                    alert("Camera scanning error: " + err);
                }
                stopScanner();
            });
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then((ignore) => {
                    // Stopping finished.
                    document.getElementById('scanner-modal').classList.add('hidden');
                }).catch((err) => {
                    // Stop failed, handle it.
                    console.warn("Failed to stop scanner", err);
                    document.getElementById('scanner-modal').classList.add('hidden');
                });
            } else {
                document.getElementById('scanner-modal').classList.add('hidden');
            }
        }
    </script>

    <script>
        // Loyalty Variables
        let selectedPatient = null;
        let loyaltyPointValue = {{ $settings->loyalty_point_value ?? 0.10 }};
        let searchTimeout = null;

        // Cart Variables
        let cart = JSON.parse(localStorage.getItem('pos_cart')) || [];
        let total = 0;

        // Init UI on load
        document.addEventListener('DOMContentLoaded', () => {
            updateCartUI();
            updateHeldCount();
        });

        function clearCart() {
            if (cart.length === 0) return;
            if (confirm('Clear current cart?')) {
                cart = [];
                updateCartUI();
            }
        }

        function holdCart() {
            if (cart.length === 0) {
                alert('Cart is empty!');
                return;
            }
            const name = prompt("Enter reference name (optional):", "Customer " + new Date().toLocaleTimeString());
            if (name === null) return; // Cancelled

            let heldCarts = JSON.parse(localStorage.getItem('pos_held_carts')) || [];
            heldCarts.push({
                name: name || 'Unnamed',
                time: new Date().toLocaleString(),
                items: cart,
                patient: selectedPatient
            });
            localStorage.setItem('pos_held_carts', JSON.stringify(heldCarts));

            cart = [];
            selectedPatient = null;
            detachPatient(); // reset UI
            updateCartUI();
            updateHeldCount();
            alert('Cart held!');
        }

        function updateHeldCount() {
            let heldCarts = JSON.parse(localStorage.getItem('pos_held_carts')) || [];
            const badge = document.getElementById('held-count');
            if (heldCarts.length > 0) {
                badge.innerText = heldCarts.length;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        function showHeldCarts() {
            let heldCarts = JSON.parse(localStorage.getItem('pos_held_carts')) || [];
            const list = document.getElementById('held-carts-list');
            list.innerHTML = '';

            if (heldCarts.length === 0) {
                list.innerHTML = '<p class="text-gray-500 text-center">No held carts.</p>';
            } else {
                heldCarts.forEach((hc, index) => {
                    const div = document.createElement('div');
                    div.className = 'flex justify-between items-center bg-gray-100 p-2 rounded';
                    div.innerHTML = `
                        <div>
                            <div class="font-bold text-sm">${hc.name}</div>
                            <div class="text-xs text-gray-500">${hc.items.length} items | ${hc.time}</div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="restoreCart(${index})" class="text-blue-600 font-bold text-sm">Restore</button>
                            <button onclick="deleteHeldCart(${index})" class="text-red-600 font-bold text-sm">X</button>
                        </div>
                     `;
                    list.appendChild(div);
                });
            }
            document.getElementById('held-carts-modal').classList.remove('hidden');
        }

        function restoreCart(index) {
            if (cart.length > 0) {
                if (!confirm('Current cart is not empty. Overwrite?')) return;
            }

            let heldCarts = JSON.parse(localStorage.getItem('pos_held_carts')) || [];
            const held = heldCarts[index];

            cart = held.items;
            if (held.patient) {
                selectPatient(held.patient);
            } else {
                detachPatient();
            }

            // Remove from held? Or keep until explicit delete? 
            // Usually restore = move back to active.
            heldCarts.splice(index, 1);
            localStorage.setItem('pos_held_carts', JSON.stringify(heldCarts));

            updateCartUI();
            updateHeldCount();
            document.getElementById('held-carts-modal').classList.add('hidden');
        }

        function deleteHeldCart(index) {
            if (!confirm('Delete this saved cart?')) return;
            let heldCarts = JSON.parse(localStorage.getItem('pos_held_carts')) || [];
            heldCarts.splice(index, 1);
            localStorage.setItem('pos_held_carts', JSON.stringify(heldCarts));
            showHeldCarts(); // Refresh list
            updateHeldCount();
        }

        async function addToCart(product) {
            // 1. Check for Interactions
            const cartIds = cart.map(item => item.id);
            if (cartIds.length > 0) {
                try {
                    const res = await fetch('{{ route('pos.check-interactions') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            cart_ids: cartIds,
                            new_product_id: product.id
                        })
                    });
                    const data = await res.json();

                    if (data.interactions && data.interactions.length > 0) {
                        let msg = "⚠️ DRUG INTERACTION WARNING ⚠️\n\n";
                        data.interactions.forEach(i => {
                            msg += `- ${i.drug} (${i.severity}): ${i.description}\n`;
                        });
                        msg += "\nDo you still want to add this product?";

                        if (!confirm(msg)) {
                            return; // Cancel add
                        }
                    }
                } catch (e) {
                    console.error("Interaction check failed", e);
                }
            }

            // 2. Add to Cart
            const existing = cart.find(item => item.id == product.id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({
                    ...product,
                    qty: 1
                });
            }
            updateCartUI();
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCartUI();
        }

        function updateQty(index, qty) {
            if (qty < 1) return;
            cart[index].qty = parseInt(qty);
            updateCartUI();
        }

        function updateCartUI() {
            // Save persistence
            localStorage.setItem('pos_cart', JSON.stringify(cart));

            const container = document.getElementById('cart-items');
            const totalEl = document.getElementById('cart-total');
            const checkoutBtn = document.getElementById('checkout-btn');

            container.innerHTML = '';
            total = 0;

            if (cart.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-400 mt-10">Cart is empty</div>';
                totalEl.innerText = 'GHS 0.00';
                checkoutBtn.disabled = true;
                return;
            }

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.qty;
                total += itemTotal;

                const div = document.createElement('div');
                div.className = 'flex justify-between items-center mb-3 p-2 bg-gray-50 rounded';
                div.innerHTML = `
                    <div class="flex-1">
                        <div class="font-bold text-sm">${item.name}</div>
                        <div class="text-xs text-gray-500">GHS ${item.price} x 
                            <input type="number" min="1" value="${item.qty}" 
                            class="w-12 border rounded px-1" 
                            onchange="updateQty(${index}, this.value)">
                        </div>
                    </div>
                    <div class="font-bold text-sm px-2">GHS ${itemTotal.toFixed(2)}</div>
                    <button onclick="removeFromCart(${index})" class="text-red-500 hover:text-red-700">x</button>
                `;
                container.appendChild(div);
            });

            totalEl.innerText = 'GHS ' + total.toFixed(2);
            checkoutBtn.disabled = false;

            // Recalculate redemption if active
            calculateRedemption();
        }

        function filterProducts() {
            const query = document.getElementById('search').value.toLowerCase();
            const products = document.querySelectorAll('.product-card');

            products.forEach(card => {
                const name = card.getAttribute('data-name');
                const barcode = card.getAttribute('data-barcode');

                if (name.includes(query) || (barcode && barcode.includes(query))) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function handleBarcodeScan(e) {
            if (e.key === 'Enter') {
                const query = document.getElementById('search').value.trim();
                const products = document.querySelectorAll('.product-card');

                // Find EXACT barcode match
                let match = null;
                for (let card of products) {
                    if (card.getAttribute('data-barcode') === query) {
                        match = card;
                        break;
                    }
                }

                if (match) {
                    match.click(); // Trigger addToCart
                    document.getElementById('search').value = ''; // Clear input
                    filterProducts(); // Reset grid
                    e.preventDefault();
                }
            }
        }

        function calculateChange() {
            const tendered = parseFloat(document.getElementById('amount-tendered').value) || 0;
            const redeemPoints = document.getElementById('redeem-check')?.checked ? (document.getElementById('redeem-amount').value || 0) : 0;

            let payable = total;
            if (redeemPoints > 0) {
                payable -= (redeemPoints * loyaltyPointValue);
            }
            if (payable < 0) payable = 0;

            const change = tendered - payable;
            const display = document.getElementById('change-display');
            const amountEl = document.getElementById('change-amount');

            if (tendered > 0) {
                display.style.display = 'flex';
                amountEl.innerText = 'GHS ' + change.toFixed(2);
                if (change < 0) amountEl.classList.add('text-red-500');
                else amountEl.classList.remove('text-red-500');
            } else {
                display.style.display = 'none';
            }
        }

        function debounceSearchPatient() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(searchPatient, 300);
        }

        async function searchPatient() {
            const query = document.getElementById('patient-search').value;
            const resultsDiv = document.getElementById('patient-results');

            if (query.length < 2) {
                resultsDiv.classList.add('hidden');
                return;
            }

            try {
                const res = await fetch(`/patients/search?query=${query}`);
                const patients = await res.json();

                resultsDiv.innerHTML = '';
                if (patients.length > 0) {
                    patients.forEach(p => {
                        const div = document.createElement('div');
                        div.className = 'p-2 hover:bg-gray-100 cursor-pointer text-sm border-b';
                        div.innerHTML = `<b>${p.name}</b> <br> <span class="text-xs text-gray-500">${p.phone} | Pts: ${p.loyalty_points}</span>`;
                        div.onclick = () => selectPatient(p);
                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.classList.remove('hidden');
                } else {
                    resultsDiv.classList.add('hidden');
                }
            } catch (e) {
                console.error(e);
            }
        }

        function selectPatient(patient) {
            selectedPatient = patient;
            document.getElementById('patient-results').classList.add('hidden');
            document.getElementById('patient-search').value = '';

            // Show Info
            document.getElementById('selected-patient-info').classList.remove('hidden');
            document.getElementById('sp-name').innerText = patient.name;
            document.getElementById('sp-points').innerText = patient.loyalty_points;

            // Auto-fill phone/email if available
            if (patient.phone) document.getElementById('customer-phone').value = patient.phone;
            if (patient.email) document.getElementById('customer-email').value = patient.email;
        }

        function detachPatient() {
            selectedPatient = null;
            document.getElementById('selected-patient-info').classList.add('hidden');
            document.getElementById('customer-phone').value = '';
            document.getElementById('customer-email').value = '';
            document.getElementById('redeem-check').checked = false;
            toggleRedemption();
        }

        function toggleRedemption() {
            const isChecked = document.getElementById('redeem-check').checked;
            const inputDiv = document.getElementById('redeem-input-div');
            if (isChecked) {
                inputDiv.classList.remove('hidden');
                // Default to max points
                document.getElementById('redeem-amount').value = selectedPatient.loyalty_points;
                calculateRedemption();
            } else {
                inputDiv.classList.add('hidden');
                calculateRedemption(); // Reset
            }
        }

        function calculateRedemption() {
            if (!document.getElementById('redeem-check').checked) {
                // Remove discount visually from total? 
                // For now, we handle logic in backend, but frontend total display 
                // might need adjustment if we want to show "Net Payable".
                // Let's just update the "Value" text for now.
                document.getElementById('redeem-value').innerText = '0.00';
                return;
            }

            let points = parseInt(document.getElementById('redeem-amount').value) || 0;
            if (points > selectedPatient.loyalty_points) {
                points = selectedPatient.loyalty_points;
                document.getElementById('redeem-amount').value = points;
            }

            const value = points * loyaltyPointValue;
            document.getElementById('redeem-value').innerText = value.toFixed(2);
        }

        async function openQuickRegister() {
            const name = prompt("Enter Patient Name:");
            if (!name) return;
            const phone = prompt("Enter Patient Phone:");
            if (!phone) return;

            try {
                const res = await fetch('{{ route('patients.api.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ name, phone })
                });
                const data = await res.json();
                if (data.success) {
                    selectPatient(data.patient);
                } else {
                    alert('Error creating patient');
                }
            } catch (e) {
                alert('Error: ' + e.message);
            }
        }

        async function checkout() {
            const method = document.getElementById('payment-method').value;
            const tendered = parseFloat(document.getElementById('amount-tendered').value) || 0;
            const redeemPoints = document.getElementById('redeem-check').checked ? (document.getElementById('redeem-amount').value || 0) : 0;

            // Calculate Payble
            let payable = total;
            if (redeemPoints > 0) {
                payable -= (redeemPoints * loyaltyPointValue);
            }
            if (payable < 0) payable = 0;

            if (tendered < payable) {
                alert(`Amount tendered is less than payable amount (GHS ${payable.toFixed(2)})!`);
                return;
            }

            // Removed blocking confirm() for smoother flow & testing
            // if (!confirm('Process transaction?')) return;

            try {
                const response = await fetch('{{ route('pos.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        cart: cart,
                        total: total, // We send raw total, backend calculates points
                        payment_method: method,
                        amount_tendered: tendered,
                        email: document.getElementById('customer-email').value,
                        phone: document.getElementById('customer-phone').value,
                        patient_id: selectedPatient ? selectedPatient.id : null,
                        redeem_points: redeemPoints
                    })
                });


                const data = await response.json();

                if (data.success) {
                    // Clear Persistence
                    localStorage.removeItem('pos_cart');
                    // Redirect to receipt
                    window.location.href = '/pos/receipt/' + data.sale_id;
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error(error);
                alert('An error occurred processing the sale.');
            }
        }
    </script>
</x-app-layout>