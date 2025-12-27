<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Prescription') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ medications: [{name: '', dosage: '', frequency: ''}] }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('prescriptions.store') }}" method="POST">
                        @csrf

                        <!-- Patient -->
                        <div class="mb-4">
                            <label for="patient_id" class="block text-gray-700 text-sm font-bold mb-2">Patient</label>
                            <select name="patient_id" id="patient_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                                <option value="">Select Patient...</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Clinical Notes</label>
                            <textarea name="notes" id="notes"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>

                        <h3 class="text-lg font-bold mb-2">Medications</h3>
                        <div class="mb-4 space-y-4">
                            <!-- Passing products to JS -->
                            <script>
                                const availableProducts = @json($products);
                            </script>

                            <template x-for="(med, index) in medications" :key="index">
                                <div class="bg-gray-50 p-4 rounded border relative"
                                    x-data="{ search: '', showResults: false }">
                                    <div class="flex justify-between mb-2">
                                        <h4 class="font-bold text-sm text-gray-700" x-text="'Drug #' + (index + 1)">
                                        </h4>
                                        <button type="button"
                                            @click="medications = medications.filter((_, i) => i !== index)"
                                            class="text-red-500 text-sm font-bold"
                                            x-show="medications.length > 1">Remove</button>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                                        <!-- Searchable Product Selection -->
                                        <div class="col-span-1 md:col-span-2 relative">
                                            <label class="block text-gray-700 text-xs font-bold mb-1">Select
                                                Medication</label>

                                            <!-- Hidden Input for ID -->
                                            <input type="hidden" :name="'medications['+index+'][product_id]'"
                                                x-model="med.product_id">

                                            <!-- Search Input -->
                                            <input type="text" x-model="search" @focus="showResults = true"
                                                @click.away="showResults = false" @input="showResults = true"
                                                placeholder="Search drug name..."
                                                class="shadow border rounded w-full py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                autocomplete="off">

                                            <!-- Dropdown Results -->
                                            <div x-show="showResults && search.length > 0"
                                                class="absolute z-10 w-full bg-white shadow-lg border rounded mt-1 max-h-48 overflow-y-auto">
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
                                                    " class="p-2 hover:bg-gray-100 cursor-pointer text-sm border-b">
                                                        <div class="font-bold" x-text="product.name"></div>
                                                        <div class="text-xs text-gray-500">
                                                            <span x-text="product.stock + ' in stock'"></span>
                                                            <span x-show="product.dosage"> | <span
                                                                    x-text="product.dosage"></span></span>
                                                        </div>
                                                    </div>
                                                </template>
                                                <!-- No Results -->
                                                <div x-show="availableProducts.filter(p => p.name.toLowerCase().includes(search.toLowerCase())).length === 0"
                                                    class="p-2 text-gray-500 text-xs text-center">No matches found</div>
                                            </div>
                                        </div>

                                        <!-- Med Name (Manual Override) -->
                                        <div class="col-span-1 md:col-span-2">
                                            <label class="block text-gray-700 text-xs font-bold mb-1">Drug Name
                                                (Editable)</label>
                                            <input type="text" :name="'medications['+index+'][name]'" x-model="med.name"
                                                placeholder="Medication Name"
                                                class="shadow border rounded w-full py-2 px-3 text-sm" required>
                                        </div>

                                        <!-- Dosage -->
                                        <div>
                                            <label class="block text-gray-700 text-xs font-bold mb-1">Dosage</label>
                                            <input type="text" :name="'medications['+index+'][dosage]'"
                                                x-model="med.dosage" placeholder="e.g. 500mg"
                                                class="shadow border rounded w-full py-2 px-3" required>
                                        </div>

                                        <!-- Frequency -->
                                        <div>
                                            <label class="block text-gray-700 text-xs font-bold mb-1">Frequency</label>
                                            <input type="text" :name="'medications['+index+'][frequency]'"
                                                x-model="med.frequency" placeholder="e.g. 1-0-1"
                                                class="shadow border rounded w-full py-2 px-3" required>
                                        </div>

                                        <!-- Route -->
                                        <div>
                                            <label class="block text-gray-700 text-xs font-bold mb-1">Route</label>
                                            <select :name="'medications['+index+'][route]'" x-model="med.route"
                                                class="shadow border rounded w-full py-2 px-3 text-sm">
                                                <option value="">Select...</option>
                                                <option value="Oral">Oral</option>
                                                <option value="IV">IV</option>
                                                <option value="IM">IM</option>
                                                <option value="SC">Subcutaneous</option>
                                                <option value="Topical">Topical</option>
                                                <option value="Inhalation">Inhalation</option>
                                            </select>
                                        </div>

                                        <!-- Form -->
                                        <div>
                                            <label class="block text-gray-700 text-xs font-bold mb-1">Form</label>
                                            <input type="text" :name="'medications['+index+'][form]'" x-model="med.form"
                                                placeholder="e.g. Tablet"
                                                class="shadow border rounded w-full py-2 px-3">
                                        </div>

                                        <!-- Quantity -->
                                        <div>
                                            <label class="block text-gray-700 text-xs font-bold mb-1">Quantity</label>
                                            <input type="number" :name="'medications['+index+'][quantity]'"
                                                x-model="med.quantity" placeholder="Qty" min="1"
                                                class="shadow border rounded w-full py-2 px-3">
                                        </div>

                                        <!-- Days Supply -->
                                        <div>
                                            <label class="block text-gray-700 text-xs font-bold mb-1">Days
                                                Supply</label>
                                            <input type="number" :name="'medications['+index+'][days_supply]'"
                                                x-model="med.days_supply" placeholder="Days" min="1"
                                                class="shadow border rounded w-full py-2 px-3">
                                        </div>

                                        <!-- Refill Reminder -->
                                        <div class="col-span-full flex items-center mt-2">
                                            <input type="hidden" :name="'medications['+index+'][refill_reminder]'"
                                                :value="med.refill_reminder ? '1' : '0'">
                                            <input type="checkbox" x-model="med.refill_reminder"
                                                class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            <label class="ml-2 block text-sm text-gray-900">
                                                Set Refill Reminder (SMS will be scheduled based on Days Supply)
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </template>
                        </div>

                        <button type="button"
                            @click="medications.push({name: '', product_id: '', dosage: '', frequency: '', quantity: ''})"
                            class="text-sm text-blue-600 mb-6 font-bold">+ Add Another Medication</button>

                        <div class="flex items-center justify-between border-t pt-4">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Submit Prescription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>