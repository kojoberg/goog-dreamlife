<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-10"
        x-data="{ medications: [{name: '', product_id: '', dosage: '', frequency: '', quantity: '', days_supply: '', refill_reminder: false}] }">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">New Prescription</h1>
                <p class="text-slate-500 text-sm mt-1">Create and dispense a new prescription.</p>
            </div>
            <a href="{{ route('prescriptions.index') }}"
                class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold py-2 px-4 rounded-lg shadow-sm transition">
                &larr; Back to List
            </a>
        </div>

        <form action="{{ route('prescriptions.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Patient & Notes -->
                <div class="lg:col-span-1 space-y-6">
                    <x-card>
                        <h3 class="text-lg font-semibold text-slate-800 mb-4 border-b pb-2">Patient Details</h3>

                        <!-- Patient Select with Add Button -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Patient <span
                                    class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <select name="patient_id" id="patient_id" required
                                    class="block w-full rounded-md border-slate-300 py-2.5 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select Patient...</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="openPatientModal()"
                                    class="bg-green-600 text-white px-3 py-2 rounded-md shadow hover:bg-green-700 transition"
                                    title="Add New Patient">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <x-form-textarea name="notes" label="Clinical Notes" rows="4"
                            placeholder="Diagnosis, instructions..." />
                    </x-card>
                </div>

                <!-- Right Column: Medications -->
                <div class="lg:col-span-2">
                    <x-card>
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg font-semibold text-slate-800">Medications</h3>
                            <button type="button"
                                @click="medications.push({name: '', product_id: '', dosage: '', frequency: '', quantity: '', days_supply: '', refill_reminder: false})"
                                class="text-sm text-indigo-600 hover:text-indigo-800 font-bold flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Drug
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Passing products to JS -->
                            <script>
                                const availableProducts = @json($products);
                            </script>

                            <template x-for="(med, index) in medications" :key="index">
                                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 relative group"
                                    x-data="{ search: '', showResults: false }">

                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="font-bold text-xs text-slate-500 uppercase tracking-wide"
                                            x-text="'Item ' + (index + 1)"></h4>
                                        <button type="button"
                                            @click="medications = medications.filter((_, i) => i !== index)"
                                            class="text-red-400 hover:text-red-600 transition"
                                            x-show="medications.length > 1" title="Remove">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <!-- Searchable Product Selection -->
                                        <div class="col-span-1 md:col-span-2 relative">
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Drug
                                                Search</label>

                                            <!-- Hidden Input for ID -->
                                            <input type="hidden" :name="'medications['+index+'][product_id]'"
                                                x-model="med.product_id">

                                            <!-- Search Input -->
                                            <input type="text" x-model="search" @focus="showResults = true"
                                                @click.away="showResults = false" @input="showResults = true"
                                                placeholder="Type to search inventory..."
                                                class="block w-full rounded-md border-slate-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                autocomplete="off">

                                            <!-- Dropdown Results -->
                                            <div x-show="showResults && search.length > 0"
                                                class="absolute z-10 w-full bg-white shadow-xl border border-slate-200 rounded-md mt-1 max-h-48 overflow-y-auto"
                                                style="display: none;">
                                                <template
                                                    x-for="product in availableProducts.filter(p => p.name.toLowerCase().includes(search.toLowerCase()))">
                                                    <div @click="
                                                        med.product_id = product.id;
                                                        med.name = product.name;
                                                        med.dosage = product.dosage || '';
                                                        med.form = product.form || ''; 
                                                        med.route = product.route || '';
                                                        search = product.name;
                                                        showResults = false;
                                                    "
                                                        class="p-2 hover:bg-slate-100 cursor-pointer text-sm border-b last:border-b-0">
                                                        <div class="font-medium text-slate-800" x-text="product.name">
                                                        </div>
                                                        <div class="text-xs text-slate-500 flex justify-between">
                                                            <span x-text="product.stock + ' in stock'"></span>
                                                            <span x-show="product.dosage"
                                                                x-text="product.dosage"></span>
                                                        </div>
                                                    </div>
                                                </template>
                                                <div x-show="availableProducts.filter(p => p.name.toLowerCase().includes(search.toLowerCase())).length === 0"
                                                    class="p-2 text-slate-500 text-xs text-center italic">No inventory
                                                    matches found</div>
                                            </div>
                                        </div>

                                        <!-- Med Name (Manual Override) -->
                                        <div class="col-span-1 md:col-span-2">
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Prescription
                                                Name</label>
                                            <input type="text" :name="'medications['+index+'][name]'" x-model="med.name"
                                                placeholder="Drug Name"
                                                class="block w-full rounded-md border-slate-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                required>
                                        </div>

                                        <!-- Dosage -->
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Dosage</label>
                                            <input type="text" :name="'medications['+index+'][dosage]'"
                                                x-model="med.dosage" placeholder="e.g. 500mg"
                                                class="block w-full rounded-md border-slate-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                required>
                                        </div>

                                        <!-- Frequency -->
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-slate-700 mb-1">Frequency</label>
                                            <select :name="'medications['+index+'][frequency]'" x-model="med.frequency"
                                                class="block w-full rounded-md border-slate-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="">Select...</option>
                                                <option value="OD">OD (Once Daily)</option>
                                                <option value="BD">BD (Twice Daily)</option>
                                                <option value="TDS">TDS (3x Daily)</option>
                                                <option value="QDS">QDS (4x Daily)</option>
                                                <option value="PRN">PRN (As needed)</option>
                                                <option value="STAT">STAT (Now)</option>
                                                <option value="Nocte">Nocte (Night)</option>
                                                <option value="Mane">Mane (Morning)</option>
                                            </select>
                                        </div>

                                        <!-- Route -->
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Route</label>
                                            <select :name="'medications['+index+'][route]'" x-model="med.route"
                                                class="block w-full rounded-md border-slate-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="">Select...</option>
                                                <option value="Oral">Oral</option>
                                                <option value="IV">IV</option>
                                                <option value="IM">IM</option>
                                                <option value="SC">Subcutaneous</option>
                                                <option value="Topical">Topical</option>
                                            </select>
                                        </div>

                                        <!-- Quantity -->
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Qty</label>
                                            <input type="number" :name="'medications['+index+'][quantity]'"
                                                x-model="med.quantity" placeholder="Qty" min="1"
                                                class="block w-full rounded-md border-slate-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>

                                        <!-- Days Supply -->
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Days</label>
                                            <input type="number" :name="'medications['+index+'][days_supply]'"
                                                x-model="med.days_supply" placeholder="Days" min="1"
                                                class="block w-full rounded-md border-slate-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>

                                        <!-- Refill Reminder -->
                                        <div class="col-span-full flex items-center pt-2">
                                            <input type="hidden" :name="'medications['+index+'][refill_reminder]'"
                                                :value="med.refill_reminder ? '1' : '0'">
                                            <input type="checkbox" x-model="med.refill_reminder"
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                            <label class="ml-2 block text-sm text-slate-600">
                                                Enable Auto-SMS Refill Reminder
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                    </x-card>

                    <div class="mt-6 flex justify-end">
                        <x-primary-button class="px-8 py-3 text-lg shadow-lg">
                            Dispense Prescription
                        </x-primary-button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

<!-- Patient Modal (Cleaned up styling) -->
<x-modal name="patient-modal" id="patient-modal" show="false" focusable>
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Add New Patient</h2>
        <p id="patient-error" class="text-sm text-red-600 mb-4 hidden"></p>

        <div class="space-y-4">
            <x-form-input id="new_patient_name" name="new_name" label="Full Name" required />
            <x-form-input id="new_patient_phone" name="new_phone" label="Phone Number" />
            <x-form-input id="new_patient_email" name="new_email" label="Email Address" type="email" />
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button onclick="closePatientModal()"
                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
            <button onclick="savePatient()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Save Patient</button>
        </div>
    </div>
</x-modal>

<script>
    // Logic remains mostly the same, updated to target new IDs if changed (x-form-input uses name as ID by default)
    function openPatientModal() {
        document.getElementById('patient-modal').classList.remove('hidden');
        document.getElementById('patient-modal').classList.add('flex'); // Align center
        document.getElementById('new_patient_name').value = '';
        document.getElementById('new_patient_phone').value = '';
        document.getElementById('new_patient_email').value = '';
        document.getElementById('patient-error').classList.add('hidden');
    }

    function closePatientModal() {
        document.getElementById('patient-modal').classList.add('hidden');
        document.getElementById('patient-modal').classList.remove('flex');
    }

    async function savePatient() {
        // IDs generated by x-form-input match the 'name' attribute usually? 
        // x-form-input: id="name"
        const name = document.getElementById('new_patient_name').value.trim();
        const phone = document.getElementById('new_patient_phone').value.trim();
        const email = document.getElementById('new_patient_email').value.trim();

        if (!name) {
            document.getElementById('patient-error').textContent = "Name is required.";
            document.getElementById('patient-error').classList.remove('hidden');
            return;
        }

        try {
            const res = await fetch('{{ route('patients.api.store') }}', { // Use API route!
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name, phone, email })
            });

            const data = await res.json();

            if (data.success) {
                const select = document.getElementById('patient_id');
                const option = new Option(data.patient.name, data.patient.id);
                select.add(option, undefined);
                select.value = data.patient.id;
                closePatientModal();
            } else {
                document.getElementById('patient-error').textContent = data.message || 'Error creating patient';
                document.getElementById('patient-error').classList.remove('hidden');
            }
        } catch (e) {
            console.error(e);
            document.getElementById('patient-error').textContent = "Failed to save.";
            document.getElementById('patient-error').classList.remove('hidden');
        }
    }
</script>