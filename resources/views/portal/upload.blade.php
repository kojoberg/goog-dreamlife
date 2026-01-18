<x-portal-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload Prescription') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Instructions -->
                    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">How it works</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ol class="list-decimal ml-4 space-y-1">
                                        <li>Upload a clear photo or scan of your prescription</li>
                                        <li>Add any notes or special instructions</li>
                                        <li>Our pharmacist will review and prepare your medication</li>
                                        <li>We'll contact you when it's ready for pickup or delivery</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('portal.upload.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- File Upload -->
                        <div class="mb-6">
                            <label for="prescription_file" class="block text-sm font-medium text-gray-700 mb-2">
                                Prescription Image/PDF <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-500 transition"
                                x-data="{ fileName: '' }">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="prescription_file"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload a file</span>
                                            <input id="prescription_file" name="prescription_file" type="file"
                                                class="sr-only" accept=".pdf,.jpg,.jpeg,.png"
                                                @change="fileName = $event.target.files[0]?.name || ''" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, PNG, JPG up to 5MB</p>
                                    <p x-show="fileName" x-text="fileName"
                                        class="text-sm text-indigo-600 font-medium mt-2"></p>
                                </div>
                            </div>
                            @error('prescription_file')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Notes (Optional)
                            </label>
                            <textarea id="notes" name="notes" rows="4"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="E.g., Preferred pickup time, allergies, delivery instructions...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Preference -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                How should we contact you?
                            </label>
                            <div class="flex items-center space-x-6">
                                <label class="flex items-center">
                                    <input type="radio" name="contact_preference" value="phone"
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" checked>
                                    <span class="ml-2 text-sm text-gray-700">Phone Call</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="contact_preference" value="sms"
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">SMS</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="contact_preference" value="email"
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Email</span>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Upload Prescription
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Uploads -->
            @php
                $recentUploads = Auth::user()->patient->documents()
                    ->where('type', 'prescription_upload')
                    ->latest()
                    ->take(5)
                    ->get();
            @endphp

            @if($recentUploads->count() > 0)
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Uploads</h3>
                        <ul class="divide-y divide-gray-200">
                            @foreach($recentUploads as $upload)
                                <li class="py-3 flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $upload->title }}</p>
                                        <p class="text-sm text-gray-500">{{ $upload->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        Pending Review
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-portal-layout>