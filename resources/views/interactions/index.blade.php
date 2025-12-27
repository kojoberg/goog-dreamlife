<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Drug Interactions') }}
            </h2>
        <div class="flex space-x-2">
            <form action="{{ route('drug-interactions.sync') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('This process interacts with external APIs and may take some time. Continue?');">
                    Sync All (FDA/NLM)
                </button>
            </form>
            <a href="{{ route('drug-interactions.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Interaction
            </a>
        </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Drug A
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Drug B
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Severity
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($interactions as $interaction)
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold">
                                            {{ $interaction->drugA->name }}
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm font-bold">
                                            {{ $interaction->drugB->name }}
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            @php
                                                $color = match($interaction->severity) {
                                                    'mild' => 'bg-yellow-200 text-yellow-800',
                                                    'moderate' => 'bg-orange-200 text-orange-800',
                                                    'severe' => 'bg-red-200 text-red-800',
                                                    default => 'bg-gray-200 text-gray-800',
                                                };
                                            @endphp
                                            <span class="inline-block px-3 py-1 font-semibold rounded-full text-xs {{ $color }}">
                                                {{ ucfirst($interaction->severity) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            {{ $interaction->description }}
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <form action="{{ route('drug-interactions.destroy', $interaction) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $interactions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
