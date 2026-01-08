<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Patient Profile') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('patients.edit', $patient) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Edit Profile
                </a>
                <a href="{{ route('patients.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Top Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Profile Summary -->
                <div
                    class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 flex items-center space-x-4">
                    <div
                        class="h-16 w-16 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-2xl">
                        {{ substr($patient->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $patient->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $patient->phone ?? 'No Phone' }} |
                            {{ $patient->email ?? 'No Email' }}</p>
                        <div class="mt-2 text-xs">
                            @if($patient->user_id)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Portal Active
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    Offline
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Loyalty -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6">
                    <div class="text-sm font-medium text-gray-500">Loyalty Points</div>
                    <div class="text-3xl font-bold text-indigo-600 mt-1">{{ $patient->loyalty_points }}</div>
                    <a href="{{ route('patients.loyalty', $patient) }}"
                        class="text-xs text-indigo-500 hover:text-indigo-700 hover:underline mt-1 block">View History
                        &rarr;</a>
                </div>

                <!-- Total Spend (Approx) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6">
                    <div class="text-sm font-medium text-gray-500">Total Spend</div>
                    <div class="text-3xl font-bold text-gray-900 mt-1">GHS
                        {{ number_format($patient->sales->sum('total_amount'), 2) }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $patient->sales->count() }} Orders</div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: Clinical & History (2/3 width) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Clinical Profile -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Clinical Profile</h3>
                            <span class="text-xs text-gray-500">Medical History & Allergies</span>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                                <h4 class="text-sm font-bold text-red-800 uppercase tracking-wide mb-2">Allergies</h4>
                                <p class="text-gray-700">{{ $patient->allergies ?? 'No known allergies recorded.' }}</p>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                <h4 class="text-sm font-bold text-blue-800 uppercase tracking-wide mb-2">Medical History
                                </h4>
                                <p class="text-gray-700">
                                    {{ $patient->medical_history ?? 'No medical history recorded.' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs for History -->
                    <div x-data="{ activeTab: 'prescriptions' }"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-xl min-h-[400px]">
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex" aria-label="Tabs">
                                <button @click="activeTab = 'prescriptions'"
                                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'prescriptions', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'prescriptions' }"
                                    class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm">
                                    Prescriptions
                                </button>
                                <button @click="activeTab = 'purchases'"
                                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'purchases', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'purchases' }"
                                    class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm">
                                    Purchases
                                </button>
                                <button @click="activeTab = 'documents'"
                                    :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'documents', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'documents' }"
                                    class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm">
                                    Documents
                                </button>
                            </nav>
                        </div>

                        <div class="p-6">
                            <!-- Prescriptions Tab -->
                            <div x-show="activeTab === 'prescriptions'">
                                <ul class="divide-y divide-gray-100">
                                    @forelse($patient->prescriptions as $prescription)
                                        <li class="py-4 hover:bg-gray-50 -mx-4 px-4 transition">
                                            <div class="flex justify-between">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">Dr.
                                                        {{ $prescription->doctor->name ?? 'External' }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $prescription->created_at->format('M d, Y') }}</p>
                                                </div>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $prescription->status === 'dispensed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ ucfirst($prescription->status) }}
                                                </span>
                                            </div>
                                            <div class="mt-2 text-sm text-gray-600">
                                                @foreach($prescription->items as $item)
                                                    <span
                                                        class="inline-block bg-gray-100 rounded px-2 py-1 text-xs mr-2 mb-1">{{ $item->medication_name }}
                                                        ({{ $item->dosage }})</span>
                                                @endforeach
                                            </div>
                                        </li>
                                    @empty
                                        <p class="text-gray-500 text-center py-8">No prescriptions found.</p>
                                    @endforelse
                                </ul>
                            </div>

                            <!-- Purchases Tab -->
                            <div x-show="activeTab === 'purchases'" style="display: none;">
                                <ul class="divide-y divide-gray-100">
                                    @forelse($patient->sales->sortByDesc('created_at')->take(10) as $sale)
                                        <li class="py-4 hover:bg-gray-50 -mx-4 px-4 transition">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">Order
                                                        #{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $sale->created_at->format('M d, Y H:i') }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm font-bold text-gray-900">GHS
                                                        {{ number_format($sale->total_amount, 2) }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ ucfirst($sale->payment_method ?? 'Cash') }}</p>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <p class="text-gray-500 text-center py-8">No purchases found.</p>
                                    @endforelse
                                </ul>
                            </div>

                            <!-- Documents Tab -->
                            <div x-show="activeTab === 'documents'" style="display: none;">
                                <!-- Upload Form -->
                                <form action="{{ route('patients.documents.store', $patient) }}" method="POST"
                                    enctype="multipart/form-data"
                                    class="mb-6 bg-gray-50 p-4 rounded-lg border border-dashed border-gray-300">
                                    @csrf
                                    <div class="space-y-3">
                                        <div class="flex gap-4">
                                            <div class="flex-1">
                                                <label
                                                    class="block text-xs font-medium text-gray-700 uppercase mb-1">Upload
                                                    File</label>
                                                <input type="file" name="document" required
                                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                            </div>
                                            <div class="w-1/3">
                                                <label
                                                    class="block text-xs font-medium text-gray-700 uppercase mb-1">Label</label>
                                                <input type="text" name="label" placeholder="e.g. Lab Results"
                                                    class="w-full text-sm rounded-md border-gray-300">
                                            </div>
                                            <div class="flex items-end">
                                                <button type="submit"
                                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">Upload</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div class="grid grid-cols-1 gap-4">
                                    @forelse($patient->documents as $doc)
                                        <div
                                            class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                                            <div class="flex items-center">
                                                <div
                                                    class="h-10 w-10 bg-gray-100 rounded flex items-center justify-center text-gray-500">
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $doc->label ?? $doc->filename }}</p>
                                                    <p class="text-xs text-gray-500">{{ round($doc->file_size / 1024, 2) }}
                                                        KB • {{ $doc->created_at->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                                    class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View</a>
                                                <form action="{{ route('patient-documents.destroy', $doc) }}" method="POST"
                                                    onsubmit="return confirm('Delete?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-8 text-gray-500">No documents.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Info & Actions (1/3 width) -->
                <div class="space-y-6">

                    <!-- Contact Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Contact Information</h3>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-gray-400 mr-2 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                                <span class="text-gray-600">{{ $patient->phone ?? 'Not provided' }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-gray-400 mr-2 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span class="text-gray-600 break-all">{{ $patient->email ?? 'Not provided' }}</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-gray-400 mr-2 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-gray-600">{{ $patient->address ?? 'Not provided' }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Portal Access -->
                    <div class="bg-indigo-50 overflow-hidden shadow-sm sm:rounded-xl border border-indigo-100 p-6">
                        <h3 class="text-lg font-bold text-indigo-900 mb-2">Portal Access</h3>
                        @if(!$patient->user_id)
                            <p class="text-sm text-indigo-700 mb-4">Grant this patient access to the online portal to view
                                their prescriptions and history.</p>
                            <form action="{{ route('patients.portal.enable', $patient) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full justify-center inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                    Generate Credentials
                                </button>
                            </form>
                        @else
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center p-2 bg-green-100 rounded-full mb-2">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <h4 class="text-green-800 font-bold">Access Enabled</h4>
                                <p class="text-xs text-green-600 mt-1">Credentials have been generated.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Notes / Other -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Internal Notes</h3>
                        <p class="text-sm text-gray-400 italic">No internal notes added.</p>
                        <!-- Add note form could go here -->
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>