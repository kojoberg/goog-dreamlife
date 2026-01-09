<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Generate Payroll') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.hr.payroll.store') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label for="month" class="block text-sm font-medium text-gray-700">Select Month</label>
                        <div class="flex gap-4">
                            <div class="w-1/2">
                                <select name="month_num" id="month_num" required
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select Month</option>
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ sprintf('%02d', $m) }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-1/2">
                                <select name="year" id="year" required
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach(range(date('Y') - 1, date('Y') + 1) as $y)
                                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            This will calculate salaries for all active employees with a set Basic Salary. Duplicate
                            records for the same month will be skipped.
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            {{ __('Generate Payroll') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>