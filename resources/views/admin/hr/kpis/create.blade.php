<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create KPI') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.hr.kpis.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('KPI Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                            placeholder="e.g. Monthly Sales Volume" />
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="type" :value="__('Type')" />
                            <select name="type"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="quantitative">Quantitative (Numbers)</option>
                                <option value="qualitative">Qualitative (Rating)</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="role" :value="__('Target Role (Optional)')" />
                            <select name="role"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">All Roles</option>
                                <option value="pharmacist">Pharmacist</option>
                                <option value="doctor">Doctor</option>
                                <option value="cashier">Cashier</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <x-input-label for="description" :value="__('Description / Formula')" />
                        <textarea name="description"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            rows="3"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            {{ __('Save KPI') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>