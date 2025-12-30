<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-10">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Add New Branch</h1>
                <p class="text-slate-500 text-sm mt-1">Expand operations to a new location.</p>
            </div>
            <a href="{{ route('branches.index') }}"
                class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold py-2 px-4 rounded-lg shadow-sm transition">
                &larr; Back to List
            </a>
        </div>

        <x-card class="max-w-md mx-auto">
            <form action="{{ route('branches.store') }}" method="POST" class="space-y-6">
                @csrf

                <x-form-input name="name" label="Branch Name" placeholder="e.g. Kumasi Branch" required autofocus />

                <x-form-input name="location" label="Location / Address" placeholder="e.g. Adum, Post Office Box 123" />

                <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4">
                    <label class="flex items-start">
                        <input type="checkbox" name="has_cashier" value="1"
                            class="mt-1 form-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition duration-150 ease-in-out">
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-indigo-900">Enable Cashier Mode</span>
                            <span class="block text-xs text-indigo-700 mt-1">
                                Pharmacists generate invoices only. A separate cashier finalizes payments.
                            </span>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end pt-2">
                    <x-primary-button>
                        Create Branch
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>