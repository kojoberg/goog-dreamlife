<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Appraisal Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg sm:rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="bg-indigo-700 px-6 py-4 text-white flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">PERFORMANCE REVIEW</h1>
                        <p class="text-sm opacity-90">{{ $appraisal->user->name }} - {{ $appraisal->period_month }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold">{{ $appraisal->total_score }}%</div>
                        <div class="text-xs uppercase opacity-75">Total Score</div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-6 bg-gray-50 p-4 rounded">
                        <div>
                            <strong>Reviewer:</strong> {{ $appraisal->reviewer->name ?? 'N/A' }}
                        </div>
                        <div>
                            <strong>Date:</strong> {{ $appraisal->appraisal_date?->format('d M Y') }}
                        </div>
                    </div>

                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">KPI Breakdown</h3>

                    <div class="space-y-4">
                        @foreach($appraisal->details as $detail)
                            <div class="flex items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $detail->kpi->name }}</h4>
                                    <p class="text-sm text-gray-500">
                                        {{ $detail->comments ? '"' . $detail->comments . '"' : '-' }}</p>
                                </div>
                                <div
                                    class="w-16 text-right font-bold {{ $detail->score >= 80 ? 'text-green-600' : 'text-gray-700' }}">
                                    {{ $detail->score }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($appraisal->comments)
                        <div class="mt-8">
                            <h3 class="font-bold text-gray-800 mb-2 border-b pb-2">Manager's Comments</h3>
                            <p class="text-gray-700 italic bg-gray-50 p-4 rounded border-l-4 border-indigo-400">
                                "{{ $appraisal->comments }}"
                            </p>
                        </div>
                    @endif

                    <!-- Print Button -->
                    <div class="mt-8 flex justify-end no-print">
                        <button onclick="window.print()"
                            class="px-4 py-2 bg-gray-800 text-white rounded shadow hover:bg-gray-700">Print
                            Report</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .no-print {
                display: none;
            }

            .py-12 {
                padding: 0;
            }

            .bg-white {
                box-shadow: none;
            }

            .max-w-3xl {
                max-width: 100%;
                margin: 0;
            }

            div.bg-white.shadow-lg.sm\:rounded-lg.overflow-hidden,
            div.bg-white.shadow-lg.sm\:rounded-lg.overflow-hidden * {
                visibility: visible;
            }

            div.bg-white.shadow-lg.sm\:rounded-lg.overflow-hidden {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</x-app-layout>