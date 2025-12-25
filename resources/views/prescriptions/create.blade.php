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
                        <div class="mb-4 space-y-2">
                            <template x-for="(med, index) in medications" :key="index">
                                <div class="flex gap-2">
                                    <input type="text" :name="'medications['+index+'][name]'"
                                        placeholder="Medication Name" class="shadow border rounded w-1/3 py-2 px-3"
                                        required>
                                    <input type="text" :name="'medications['+index+'][dosage]'"
                                        placeholder="Dosage (e.g. 500mg)" class="shadow border rounded w-1/3 py-2 px-3"
                                        required>
                                    <input type="text" :name="'medications['+index+'][frequency]'"
                                        placeholder="Frequency (e.g. 2x Daily)"
                                        class="shadow border rounded w-1/3 py-2 px-3" required>
                                    <button type="button"
                                        @click="medications = medications.filter((_, i) => i !== index)"
                                        class="text-red-500" x-show="medications.length > 1">x</button>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="medications.push({name: '', dosage: '', frequency: ''})"
                            class="text-sm text-blue-600 mb-6">+ Add Another Medication</button>

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