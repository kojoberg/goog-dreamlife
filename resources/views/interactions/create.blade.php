<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Drug Interaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('drug-interactions.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="drug_a_id" class="block text-gray-700 text-sm font-bold mb-2">Drug A</label>
                                <select name="drug_a_id" id="drug_a_id"
                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Select Drug A</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="drug_b_id" class="block text-gray-700 text-sm font-bold mb-2">Drug B</label>
                                <select name="drug_b_id" id="drug_b_id"
                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Select Drug B</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="severity" class="block text-gray-700 text-sm font-bold mb-2">Severity</label>
                            <select name="severity" id="severity"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="mild">Mild - Regular monitoring needed</option>
                                <option value="moderate">Moderate - Adjust dosage or monitor closely</option>
                                <option value="severe">Severe - Avoid combination</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description /
                                Effect</label>
                            <textarea name="description" id="description" rows="3"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required placeholder="e.g. Increases risk of bleeding..."></textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Save Interaction Rule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>