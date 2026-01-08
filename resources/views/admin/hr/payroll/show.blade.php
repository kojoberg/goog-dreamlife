<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payslip') }}: {{ $payroll->month_year }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg sm:rounded-lg overflow-hidden">

                <!-- Header -->
                <div class="bg-indigo-700 px-6 py-4 text-white flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">DREAM-LIFE HEALTHCARE</h1>
                        <p class="text-sm opacity-90">Payslip for
                            {{ \Carbon\Carbon::parse($payroll->month_year)->format('F Y') }}</p>
                    </div>
                    <div class="text-right text-sm">
                        <p>Date: {{ $payroll->created_at->format('d M Y') }}</p>
                        <p>Ref: #PAY-{{ $payroll->id }}</p>
                    </div>
                </div>

                <!-- Employee Info -->
                <div class="px-6 py-6 border-b border-gray-200 bg-gray-50">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Employee Name</p>
                            <p class="font-bold text-gray-900">{{ $payroll->user->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase">Job Title</p>
                            <p class="font-bold text-gray-900">{{ $payroll->user->employeeProfile->job_title ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">SSNIT Number</p>
                            <p class="text-gray-900">{{ $payroll->user->employeeProfile->ssnit_number ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase">Bank Account</p>
                            <p class="text-gray-900">{{ $payroll->user->employeeProfile->bank_name ?? '' }} -
                                {{ $payroll->user->employeeProfile->account_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Earnings -->
                <div class="px-6 py-4">
                    <h3 class="text-sm font-bold text-gray-900 uppercase border-b pb-2 mb-2">Earnings</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Basic Salary</span>
                            <span class="font-medium">{{ number_format($payroll->basic_salary, 2) }}</span>
                        </div>
                        @if($payroll->total_allowances > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-700">Allowances</span>
                                <span class="font-medium">{{ number_format($payroll->total_allowances, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between border-t border-gray-100 pt-2 mt-2">
                            <span class="text-gray-900 font-bold">Gross Salary</span>
                            <span class="font-bold">{{ number_format($payroll->gross_salary, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Deductions -->
                <div class="px-6 py-4 bg-gray-50">
                    <h3 class="text-sm font-bold text-red-700 uppercase border-b pb-2 mb-2">Deductions</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">SSNIT Tier 2 (5.5%)</span>
                            <span class="font-medium text-red-600">-{{ number_format($payroll->tier_2, 2) }}</span>
                        </div>
                        @if($payroll->tier_3 > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-700">Provident Fund (Tier 3)</span>
                                <span class="font-medium text-red-600">-{{ number_format($payroll->tier_3, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-700">PAYE Tax</span>
                            <span class="font-medium text-red-600">-{{ number_format($payroll->paye_tax, 2) }}</span>
                        </div>
                        @if($payroll->other_deductions > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-700">Other Deductions</span>
                                <span
                                    class="font-medium text-red-600">-{{ number_format($payroll->other_deductions, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                            <span class="text-gray-900 font-bold">Total Deductions</span>
                            <span
                                class="font-bold text-red-700">-{{ number_format($payroll->tier_2 + $payroll->tier_3 + $payroll->paye_tax + $payroll->other_deductions, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Net Pay -->
                <div class="px-6 py-6 bg-indigo-50 border-t border-indigo-100">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-indigo-900">NET PAYABLE</span>
                        <span class="text-2xl font-bold text-indigo-700">GHS
                            {{ number_format($payroll->net_salary, 2) }}</span>
                    </div>
                    <div class="text-right mt-1 text-xs text-indigo-500 uppercase">
                        (Employer SSNIT Tier 1 Contribution: {{ number_format($payroll->tier_1, 2) }})
                    </div>
                </div>

                <!-- Print Button -->
                <div class="px-6 py-4 bg-gray-100 flex justify-end no-print">
                    <button onclick="window.print()"
                        class="px-4 py-2 bg-gray-800 text-white rounded shadow hover:bg-gray-700">Print / Save
                        PDF</button>
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