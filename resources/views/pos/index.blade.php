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
                        <input type="text" id="search" placeholder="Search products..."
                            class="w-full mb-4 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onkeyup="filterProducts()">

                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 overflow-y-auto max-h-[600px]"
                            id="product-grid">
                            @foreach ($products as $product)
                                <div class="product-card bg-gray-50 border rounded-lg p-4 cursor-pointer hover:bg-blue-50 transition"
                                    onclick="addToCart({{ json_encode($product) }})"
                                    data-name="{{ strtolower($product['name']) }}">
                                    <div class="font-bold text-gray-800">{{ $product['name'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $product['category'] }}</div>
                                    <div class="mt-2 flex justify-between items-center">
                                        <span class="font-bold text-blue-600">GHS
                                            {{ number_format($product['price'], 2) }}</span>
                                        <span class="text-xs bg-gray-200 px-2 py-1 rounded">Qty:
                                            {{ $product['stock'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Cart (Right) -->
                <div class="lg:w-1/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 h-full flex flex-col">
                        <h3 class="text-lg font-bold mb-4">Current Order</h3>

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

    <script>
        // Loyalty Variables
        let selectedPatient = null;
        let loyaltyPointValue = {{ $settings->loyalty_point_value ?? 0.10 }};
        let searchTimeout = null;

        // Cart Variables
        let cart = [];
        let total = 0;

        function addToCart(product) {
            // Check if product needed interaction check?
            // For now just add to cart.
            // Check if already in cart
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
                if (name.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
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

            if (!confirm('Process transaction?')) return;

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