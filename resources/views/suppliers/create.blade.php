<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-10">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Register Supplier</h1>
                <p class="text-slate-500 text-sm mt-1">Add a new supplier to the procurement database.</p>
            </div>
            <a href="{{ route('suppliers.index') }}"
                class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold py-2 px-4 rounded-lg shadow-sm transition">
                &larr; Back to List
            </a>
        </div>

        <!-- Form Card -->
        <x-card class="max-w-4xl mx-auto">
            <form action="{{ route('suppliers.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Company Name -->
                    <div class="col-span-2">
                        <x-form-input name="name" label="Company Name" placeholder="e.g. Pharma Plus Ltd." required />
                    </div>

                    <!-- Contact Person -->
                    <x-form-input name="contact_person" label="Contact Person" placeholder="e.g. Jane Doe" />

                    <!-- Phone -->
                    <x-form-input name="phone" label="Phone Number" placeholder="e.g. 0244000000" />

                    <!-- Email -->
                    <div class="col-span-2">
                        <x-form-input name="email" label="Email Address" type="email"
                            placeholder="e.g. orders@pharmaplus.com" />
                    </div>

                    <!-- Address -->
                    <div class="col-span-2">
                        <x-form-textarea name="address" label="Physical Address" placeholder="Office Location..."
                            rows="2" />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end pt-6 border-t border-slate-200">
                    <x-primary-button class="px-8">
                        Save Supplier
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>