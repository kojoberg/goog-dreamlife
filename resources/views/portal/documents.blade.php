<x-portal-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Documents') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Lab Results & Files
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Download your medical documents.
                    </p>
                </div>

                @if($documents->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 p-6">
                        @foreach($documents as $document)
                            <div class="border rounded-lg p-4 hover:shadow-lg transition">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-2 bg-indigo-100 rounded-lg">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $document->created_at->format('M d, Y') }}</span>
                                </div>
                                <h4 class="font-bold text-gray-900 truncate" title="{{ $document->name }}">{{ $document->name }}
                                </h4>
                                <p class="text-sm text-gray-500 mb-4">{{ ucfirst($document->type) }}</p>

                                <a href="{{ Storage::url($document->file_path) }}" target="_blank"
                                    class="block w-full text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Download / View
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No documents</h3>
                        <p class="mt-1 text-sm text-gray-500">Tests results and referrals will appear here.</p>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-portal-layout>