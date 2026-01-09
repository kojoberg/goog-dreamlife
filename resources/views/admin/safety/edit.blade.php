<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Edit Drug Interaction</h2>
                    <a href="{{ route('admin.safety.index') }}" class="text-indigo-600 hover:text-indigo-800">&larr;
                        Back to Safety Database</a>
                </div>

                <form action="{{ route('admin.safety.update', $interaction) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="drug_a" value="Drug A" />
                            <x-text-input id="drug_a" class="block mt-1 w-full bg-gray-100" type="text"
                                value="{{ $interaction->drugA->name }}" disabled />
                            <p class="text-xs text-gray-500 mt-1">Cannot be changed. Delete and recreate if needed.</p>
                        </div>

                        <div>
                            <x-input-label for="drug_b" value="Drug B" />
                            <x-text-input id="drug_b" class="block mt-1 w-full bg-gray-100" type="text"
                                value="{{ $interaction->drugB->name }}" disabled />
                            <p class="text-xs text-gray-500 mt-1">Cannot be changed. Delete and recreate if needed.</p>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="severity" value="Severity" />
                        <select id="severity" name="severity"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="mild" {{ $interaction->severity === 'mild' ? 'selected' : '' }}>Mild</option>
                            <option value="moderate" {{ $interaction->severity === 'moderate' ? 'selected' : '' }}>
                                Moderate</option>
                            <option value="severe" {{ $interaction->severity === 'severe' ? 'selected' : '' }}>Severe
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('severity')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="4"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            required>{{ old('description', $interaction->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            Update Interaction
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>