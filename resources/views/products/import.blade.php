<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4">
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
                                    Use the template to ensure your data is formatted correctly.
                                    <a href="{{ route('products.import.template') }}"
                                        class="font-bold underline hover:text-blue-600">Download Template (CSV)</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('products.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Upload CSV File
                            </label>
                            <div id="drop-zone"
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-400 transition cursor-pointer">
                                <div class="space-y-1 text-center">
                                    <svg id="upload-icon" class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                        fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div id="file-info" class="hidden">
                                        <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p id="file-name" class="text-sm font-medium text-green-600 mt-2"></p>
                                        <p class="text-xs text-gray-500">Click to change file</p>
                                    </div>
                                    <div id="upload-prompt" class="flex text-sm text-gray-600">
                                        <label for="file-upload"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload a file</span>
                                            <input id="file-upload" name="file" type="file" class="sr-only"
                                                accept=".csv">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        CSV up to 2MB
                                    </p>
                                </div>
                            </div>
                            @error('file')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <script>
                            document.getElementById('file-upload').addEventListener('change', function (e) {
                                const file = e.target.files[0];
                                if (file) {
                                    document.getElementById('upload-icon').classList.add('hidden');
                                    document.getElementById('upload-prompt').classList.add('hidden');
                                    document.getElementById('file-info').classList.remove('hidden');
                                    document.getElementById('file-name').textContent = file.name;
                                    document.getElementById('drop-zone').classList.remove('border-gray-300');
                                    document.getElementById('drop-zone').classList.add('border-green-400', 'bg-green-50');
                                }
                            });

                            // Click anywhere in drop zone to trigger file input
                            document.getElementById('drop-zone').addEventListener('click', function () {
                                document.getElementById('file-upload').click();
                            });
                        </script>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('products.index') }}"
                                class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                                Import Products
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>