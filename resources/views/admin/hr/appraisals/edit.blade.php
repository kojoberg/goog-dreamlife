<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Grade Appraisal') }}: {{ $appraisal->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('info'))
                <div class="mb-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                    <p>{{ session('info') }}</p>
                </div>
            @endif

            <form action="{{ route('admin.hr.appraisals.update', $appraisal) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-2 gap-4 text-sm mb-6 border-b pb-4">
                        <div>
                            <span class="text-gray-500">Staff:</span> <span
                                class="font-semibold">{{ $appraisal->user->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Role:</span> <span
                                class="font-semibold">{{ ucfirst($appraisal->user->role) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Period:</span> <span
                                class="font-semibold">{{ $appraisal->period_month }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Reviewer:</span> <span
                                class="font-semibold">{{ Auth::user()->name }}</span>
                        </div>
                    </div>

                    <h3 class="font-bold text-lg mb-4 text-gray-800">Key Performance Indicators</h3>

                    @if($kpis->count() > 0)
                        <div class="space-y-6">
                            @foreach($kpis as $kpi)
                                @php
                                    $detail = $appraisal->details->where('kpi_id', $kpi->id)->first();
                                    $score = $detail ? $detail->score : 0;
                                    $comment = $detail ? $detail->comments : '';
                                @endphp
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $kpi->name }}</h4>
                                            <p class="text-xs text-gray-500">{{ $kpi->description }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span
                                                class="text-xs font-semibold bg-gray-200 px-2 py-1 rounded text-gray-700 uppercase">{{ $kpi->type }}</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700">Score (0-100)</label>
                                            <input type="number" name="scores[{{$kpi->id}}]" value="{{ $score }}" min="0"
                                                max="100"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-medium text-gray-700">Comments</label>
                                            <input type="text" name="comments[{{$kpi->id}}]" value="{{ $comment }}"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="Observation...">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 bg-yellow-50 text-yellow-700 rounded-md">
                            No KPIs found for this user role. Please define KPIs in HR Settings first.
                        </div>
                    @endif

                    <div class="mt-8 border-t pt-4">
                        <label class="block font-medium text-gray-700 mb-2">Overall Reviewer Comments</label>
                        <textarea name="overall_comment" rows="3"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $appraisal->comments }}</textarea>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-primary-button>
                            {{ __('Save & Finalize Appraisal') }}
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>