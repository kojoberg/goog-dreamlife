<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clinical Safety Database') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Warning / Intro -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            The Safety Database contains a reference list of global drug interactions. Run the <strong
                                class="font-bold">Sync</strong> process to match these references against your current
                            Inventory.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats & Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Reference Count -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6">
                    <div class="text-sm font-medium text-gray-500">Global References</div>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $referenceCount }}</p>
                    <p class="text-xs text-gray-400 mt-1">Known interaction pairs</p>
                </div>

                <!-- Active Links -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6">
                    <div class="text-sm font-medium text-gray-500">Active Inventory Links</div>
                    <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $activeLinksCount }}</p>
                    <p class="text-xs text-gray-400 mt-1">Interactions active in your stock</p>
                </div>

                <!-- Sync Action -->
                <div
                    class="bg-indigo-50 overflow-hidden shadow-sm sm:rounded-xl p-6 border border-indigo-100 flex flex-col justify-center items-center text-center">
                    <h3 class="text-lg font-bold text-indigo-900 mb-2">Sync with Inventory</h3>
                    <form action="{{ route('admin.safety.sync') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="animate-none mr-2 h-4 w-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Sync Now
                        </button>
                    </form>
                    <p class="text-xs text-indigo-500 mt-2">Matches product names to reference list</p>
                </div>
            </div>

            <!-- Active Interactions List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Active Warnings</h3>
                </div>

                @if($interactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Drug A</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Drug B</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Severity
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($interactions as $interaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $interaction->drugA->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $interaction->drugB->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $interaction->severity === 'severe' ? 'bg-red-100 text-red-800' : ($interaction->severity === 'moderate' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ ucfirst($interaction->severity) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $interaction->description }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4">
                        {{ $interactions->links() }}
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">No active interactions found in inventory. Run Sync or add
                        manually.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>