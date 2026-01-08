<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Performance Appraisals') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex justify-between items-center px-4 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Appraisal History</h3>
                    <a href="{{ route('admin.hr.appraisals.create') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                        Start New Appraisal
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff Member
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reviewer
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Score</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($appraisals as $appraisal)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $appraisal->period_month }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $appraisal->user->name }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $appraisal->user->employeeProfile->job_title ?? 'Staff' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $appraisal->reviewer->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        @if($appraisal->total_score !== null)
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $appraisal->total_score >= 80 ? 'bg-green-100 text-green-800' : ($appraisal->total_score >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $appraisal->total_score }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                        {{ $appraisal->appraisal_date?->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.hr.appraisals.show', $appraisal) }}"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        <a href="{{ route('admin.hr.appraisals.edit', $appraisal) }}"
                                            class="text-blue-600 hover:text-blue-900">Grade</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4 px-4">
                        {{ $appraisals->links() }}
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>