<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payroll Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex justify-between items-center px-4 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Payroll History</h3>
                    <a href="{{ route('admin.hr.payroll.create') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                        Generate New Payroll
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">PAYE</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net Pay
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($payrolls as $payroll)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payroll->month_year }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $payroll->user->name }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $payroll->user->employeeProfile->job_title ?? 'Staff' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                        {{ number_format($payroll->gross_salary, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                        {{ number_format($payroll->paye_tax, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                        {{ number_format($payroll->net_salary, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.hr.payroll.show', $payroll) }}"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">Payslip</a>
                                        <form action="{{ route('admin.hr.payroll.destroy', $payroll) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Delete this record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Del</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4 px-4">
                        {{ $payrolls->links() }}
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>