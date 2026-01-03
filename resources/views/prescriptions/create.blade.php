<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-10" x-data="{ 
            medications: [{name: '', product_id: '', dosage: '', frequency: '', quantity: '', days_supply: '', refill_reminder: false, price: 0}],
            showPatientModal: false,
            newPatient: { name: '', phone: '', email: '' },
            patientError: '',
            get totalCost() {
                return this.medications.reduce((sum, med) => {
                    return sum + (med.price * (med.quantity || 0));
                }, 0).toFixed(2);
            },
            async savePatient() {
                this.patientError = '';
                if (!this.newPatient.name) {
                    this.patientError = 'Name is required.';
                    return;
                }
                try {
                    const res = await fetch('{{ route('patients.api.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.newPatient)
                    });
                    const data = await res.json();
                    if (data.success) {
                        // Add to select and select it
                        const select = document.getElementById('patient_id');
                        const option = new Option(data.patient.name, data.patient.id);
                        select.add(option, undefined);
                        select.value = data.patient.id;
                        
                        // Close and Reset
                        this.showPatientModal = false;
                        this.newPatient = { name: '', phone: '', email: '' };
                    } else {
                        this.patientError = data.message || 'Error creating patient';
                    }
                } catch (e) {
                    console.error(e);
                    this.patientError = 'Failed to save patient.';
                }
            }
        }">
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
                                <button type="button" @click="showPatientModal = true"
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
                                @click="medications.push({name: '', product_id: '', dosage: '', frequency: '', quantity: '', days_supply: '', refill_reminder: false, price: 0})"
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
                                                        med.frequency = ''; // Specific to prescription, reset or keep default
                                                        med.route = product.drug_route || ''; // DB column is drug_route
                                                        med.price = parseFloat(product.unit_price || 0);
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

                    <div class="mt-6 flex flex-col items-end">
                        <div class="mb-4 text-xl font-bold text-slate-800" x-show="totalCost > 0">
                            Estimated Total: GHS <span x-text="totalCost"></span>
                        </div>
                        <x-primary-button class="px-8 py-3 text-lg shadow-lg">
                            Create Prescription
                        </x-primary-button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Custom Modal using Alpine x-show -->
        <div x-show="showPatientModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div x-show="showPatientModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="showPatientModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showPatientModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Add New Patient
                                </h3>

                                <div class="mt-4 space-y-4">
                                    <template x-if="patientError">
                                        <div class="text-red-500 text-sm font-bold" x-text="patientError"></div>
                                    </template>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                        <input type="text" x-model="newPatient.name"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                                        <input type="text" x-model="newPatient.phone"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" x-model="newPatient.email"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="savePatient()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Save Patient
                        </button>
                        <button type="button" @click="showPatientModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>