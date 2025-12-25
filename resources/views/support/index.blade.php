<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Support & SOP Manual') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="prose max-w-none">
                        <h1>Standard Operating Procedures (SOP)</h1>
                        <p class="text-gray-600">This manual outlines the standard procedures for operating the Dream
                            Life Healthcare Pharmacy Management System. All staff must adhere to these guidelines to
                            ensure compliance and efficiency.</p>

                        <div class="mt-8 space-y-8">

                            <!-- Section 1: Inventory -->
                            <section>
                                <h2 class="text-2xl font-bold text-indigo-700 border-b pb-2">1. Inventory Management
                                </h2>
                                <div class="mt-4 space-y-4">
                                    <h3 class="font-semibold text-lg">Receiving Stock</h3>
                                    <ul class="list-disc pl-5">
                                        <li>Verify the physical goods against the supplier's invoice.</li>
                                        <li>Navigate to <strong>Inventory > Receive Stock</strong>.</li>
                                        <li>Select the Supplier and Product. <strong>Verify the Batch Number and Expiry
                                                Date</strong> on the physical package.</li>
                                        <li>Enter the exact quantity and expiry date. <strong>Accuracy is
                                                critical</strong> for FIFO logic.</li>
                                        <li>Submit the form. This action is logged in the audit trail.</li>
                                    </ul>

                                    <h3 class="font-semibold text-lg">Stock Audits</h3>
                                    <ul class="list-disc pl-5">
                                        <li>Weekly checks of the <strong>Low Stock</strong> and <strong>Expired
                                                Batches</strong> alerts on the Dashboard.</li>
                                        <li>Physically remove expired products from shelves immediately.</li>
                                        <li>Admins must verify stock levels against the system count monthly.</li>
                                    </ul>
                                </div>
                            </section>

                            <!-- Section 2: Point of Sale -->
                            <section>
                                <h2 class="text-2xl font-bold text-indigo-700 border-b pb-2">2. Point of Sale (POS)</h2>
                                <div class="mt-4 space-y-4">
                                    <h3 class="font-semibold text-lg">Processing a Sale</h3>
                                    <ul class="list-disc pl-5">
                                        <li>Ask the customer for their prescription (if applicable).</li>
                                        <li>Search for the product in the POS grid.</li>
                                        <li>Add items to the cart. <strong>Do not override verify prices
                                                manually</strong> unless authorized.</li>
                                        <li>Select Payment Method (Cash or Mobile Money).</li>
                                        <li>Collect payment <strong>before</strong> clicking "Complete Sale".</li>
                                        <li>Print the receipt and hand it to the customer.</li>
                                    </ul>

                                    <h3 class="font-semibold text-lg">Returns & Refunds</h3>
                                    <ul class="list-disc pl-5">
                                        <li>Returns must be approved by a Supervisor/Admin.</li>
                                        <li>Verify the batch number on the returned item matches the receipt.</li>
                                    </ul>
                                </div>
                            </section>

                            <!-- Section 3: Clinical & Dispensing -->
                            <section>
                                <h2 class="text-2xl font-bold text-indigo-700 border-b pb-2">3. Clinical & Dispensing
                                </h2>
                                <div class="mt-4 space-y-4">
                                    <h3 class="font-semibold text-lg">Prescription Fulfillment</h3>
                                    <ul class="list-disc pl-5">
                                        <li>Pharmacists must review the prescription details on the <strong>Pharmacy
                                                Portal</strong>.</li>
                                        <li>Check for drug interactions or allergies (consult Patient Record).</li>
                                        <li>Mark the prescription as <strong>Dispensed</strong> only after handing over
                                            the medication.</li>
                                    </ul>
                                </div>
                            </section>

                            <!-- Section 4: System Administration -->
                            <section>
                                <h2 class="text-2xl font-bold text-indigo-700 border-b pb-2">4. System Administration
                                </h2>
                                <div class="mt-4 space-y-4">
                                    <h3 class="font-semibold text-lg">User Management</h3>
                                    <ul class="list-disc pl-5">
                                        <li>Only Admins can create new user accounts.</li>
                                        <li>Assign the correct role (Cashier, Pharmacist, Doctor) to limit access
                                            rights.</li>
                                        <li>Immediately deactivate accounts for staff who have left.</li>
                                    </ul>
                                </div>
                            </section>

                        </div>

                        <div class="mt-12 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <h3 class="font-bold text-gray-900">Need Technical Support?</h3>
                            <p class="text-gray-600">Contact the IT Department at <strong>support@dreamlife.com</strong>
                                or call ext. 101.</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>