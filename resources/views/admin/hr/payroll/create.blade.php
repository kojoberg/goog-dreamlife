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
                        <input type="month" name="month" id="month" required
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
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