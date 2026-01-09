<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit KPI') }}: {{ $kpi->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.hr.kpis.update', $kpi) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('KPI Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$kpi->name"
                            required placeholder="e.g. Monthly Sales Volume" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <x-input-label for="weight" :value="__('Weight (e.g. 10.5)')" />
                            <x-text-input id="weight" class="block mt-1 w-full" type="number" step="0.01" name="weight"
                                :value="$kpi->weight" required />
                        </div>
                        <div>
                            <input type="hidden" name="max_score" value="5">
                        </div>
                        <div>
                            <x-input-label for="category" :value="__('Category')" />
                            <select name="category"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">General</option>
                                @foreach(\App\Models\Kpi::CATEGORIES as $key => $label)
                                    <option value="{{ $key }}" {{ $kpi->category === $key ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="type" :value="__('Type')" />
                            <select name="type"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="quantitative" {{ $kpi->type === 'quantitative' ? 'selected' : '' }}>
                                    Quantitative (Numbers)</option>
                                <option value="qualitative" {{ $kpi->type === 'qualitative' ? 'selected' : '' }}>
                                    Qualitative (Rating)</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="role" :value="__('Target Role (Optional)')" />
                            <select name="role"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="" {{ is_null($kpi->role) ? 'selected' : '' }}>All Roles</option>
                                <option value="pharmacist" {{ $kpi->role === 'pharmacist' ? 'selected' : '' }}>Pharmacist
                                </option>
                                <option value="doctor" {{ $kpi->role === 'doctor' ? 'selected' : '' }}>Doctor</option>
                                <option value="lab_scientist" {{ $kpi->role === 'lab_scientist' ? 'selected' : '' }}>Lab
                                    Scientist</option>
                                <option value="cashier" {{ $kpi->role === 'cashier' ? 'selected' : '' }}>Cashier</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <x-input-label for="description" :value="__('Description / Formula')" />
                        <textarea name="description"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            rows="3">{{ $kpi->description }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.hr.kpis.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <x-primary-button>
                            {{ __('Update KPI') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>