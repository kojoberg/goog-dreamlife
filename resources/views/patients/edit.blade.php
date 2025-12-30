<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-10">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Edit Patient Record</h1>
                <p class="text-slate-500 text-sm mt-1">Update information for {{ $patient->name }}.</p>
            </div>
            <a href="{{ route('patients.index') }}"
                class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold py-2 px-4 rounded-lg shadow-sm transition">
                &larr; Back to List
            </a>
        </div>

        <!-- Form Card -->
        <x-card class="max-w-4xl mx-auto">
            <form action="{{ route('patients.update', $patient) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Personal Info Section -->
                <div class="border-b border-slate-200 pb-4 mb-4">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div class="col-span-2">
                            <x-form-input name="name" label="Full Name" :value="$patient->name" required />
                        </div>

                        <!-- Phone -->
                        <x-form-input name="phone" label="Phone Number" :value="$patient->phone" />

                        <!-- Email -->
                        <x-form-input name="email" label="Email Address" type="email" :value="$patient->email" />

                        <!-- Address -->
                        <div class="col-span-2">
                            <x-form-textarea name="address" label="Residential Address" rows="2"
                                :value="$patient->address" />
                        </div>
                    </div>
                </div>

                <!-- Medical Info Section -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Medical & Clinical Profile</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form-textarea name="medical_history" label="Medical History" rows="3"
                            :value="$patient->medical_history" />

                        <x-form-textarea name="allergies" label="Known Allergies" rows="3"
                            :value="$patient->allergies" />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end pt-6 border-t border-slate-200">
                    <x-primary-button class="px-8 bg-indigo-600 hover:bg-indigo-700">
                        Update Patient Record
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>