<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Appraisal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.hr.appraisals.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="user_id" :value="__('Select Staff Member')" />
                        <select name="user_id" id="user_id"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required>
                            <option value="">-- Select --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} ({{ $user->employeeProfile->job_title ?? ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="period_month" :value="__('Appraisal Period (Month)')" />
                        <input type="month" name="period_month" id="period_month"
                            class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required>
                    </div>

                    <div class="mb-6">
                        <x-input-label for="appraisal_date" :value="__('Date of Review')" />
                        <x-text-input id="appraisal_date" class="block mt-1 w-full" type="date" name="appraisal_date"
                            :value="date('Y-m-d')" required />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            {{ __('Start Appraisal') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>