<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-10">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Add New Patient</h1>
                <p class="text-slate-500 text-sm mt-1">Register a new patient into the system.</p>
            </div>
            <a href="{{ route('patients.index') }}"
                class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold py-2 px-4 rounded-lg shadow-sm transition">
                &larr; Back to List
            </a>
        </div>

        <!-- Form Card -->
        <x-card class="max-w-4xl mx-auto">
            <form action="{{ route('patients.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Personal Info Section -->
                <div class="border-b border-slate-200 pb-4 mb-4">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div class="col-span-2">
                            <x-form-input name="name" label="Full Name" placeholder="e.g. John Doe" required />
                        </div>

                        <!-- Phone -->
                        <x-form-input name="phone" label="Phone Number" placeholder="e.g. 0244123456" />

                        <!-- Email -->
                        <x-form-input name="email" label="Email Address" type="email"
                            placeholder="e.g. john@example.com" />

                        <!-- Address -->
                        <div class="col-span-2">
                            <x-form-textarea name="address" label="Residential Address"
                                placeholder="House Number / Street Name / City" rows="2" />
                        </div>
                    </div>
                </div>

                <!-- Medical Info Section -->
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">Medical & Clinical Profile</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form-textarea name="medical_history" label="Medical History"
                            placeholder="List chronic conditions (e.g. Hypertension, Diabetes)..." rows="3" />

                        <x-form-textarea name="allergies" label="Known Allergies"
                            placeholder="List any drug or food allergies (e.g. Penicillin, Peanuts)..." rows="3" />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end pt-6 border-t border-slate-200">
                    <x-primary-button class="px-8">
                        Save Patient Record
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>