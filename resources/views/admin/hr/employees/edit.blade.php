<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Employee Profile') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('admin.hr.employees.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Info -->
                        <div class="col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Basic Information</h3>
                        </div>

                        <div>
                            <x-input-label for="name" :value="__('Full Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                :value="$user->name" required />
                        </div>

                        <div>
                            <x-input-label for="role" :value="__('System Role')" />
                            <select name="role"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="pharmacist" {{ $user->role == 'pharmacist' ? 'selected' : '' }}>Pharmacist
                                </option>
                                <option value="cashier" {{ $user->role == 'cashier' ? 'selected' : '' }}>Cashier</option>
                                <option value="lab_scientist" {{ $user->role == 'lab_scientist' ? 'selected' : '' }}>Lab
                                    Scientist</option>
                                <option value="doctor" {{ $user->role == 'doctor' ? 'selected' : '' }}>Doctor</option>
                                <option value="nurse" {{ $user->role == 'nurse' ? 'selected' : '' }}>Nurse</option>
                                <option value="other" {{ $user->role == 'other' ? 'selected' : '' }}>Other Staff</option>
                            </select>
                        </div>

                        <!-- Employment Details -->
                        <div class="col-span-2 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Employment Details</h3>
                        </div>

                        <div>
                            <x-input-label for="job_title" :value="__('Job Title')" />
                            <x-text-input id="job_title" class="block mt-1 w-full" type="text" name="job_title"
                                :value="$user->employeeProfile->job_title" placeholder="e.g. Senior Pharmacist" />
                        </div>

                        <div>
                            <x-input-label for="department_id" :value="__('Department')" />
                            <select name="department_id"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ $user->employeeProfile->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Departments can be managed in HR Settings.</p>
                        </div>

                        <div>
                            <x-input-label for="employment_status" :value="__('Employment Status')" />
                            <select name="employment_status"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="Full-time" {{ $user->employeeProfile->employment_status == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                                <option value="Part-time" {{ $user->employeeProfile->employment_status == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                                <option value="Contract" {{ $user->employeeProfile->employment_status == 'Contract' ? 'selected' : '' }}>Contract</option>
                                <option value="Internship" {{ $user->employeeProfile->employment_status == 'Internship' ? 'selected' : '' }}>Internship</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="date_joined" :value="__('Date Joined')" />
                            <x-text-input id="date_joined" class="block mt-1 w-full" type="date" name="date_joined"
                                :value="$user->employeeProfile->date_joined?->format('Y-m-d')" />
                        </div>

                        <!-- Salary & Statutory -->
                        <div class="col-span-2 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Payroll & Statutory Data
                            </h3>
                        </div>

                        <div>
                            <x-input-label for="basic_salary" :value="__('Basic Monthly Salary (GHS)')" />
                            <x-text-input id="basic_salary" class="block mt-1 w-full" type="number" step="0.01"
                                name="basic_salary" :value="$user->employeeProfile->basic_salary" />
                        </div>

                        <div>
                            <x-input-label for="ssnit_number" :value="__('SSNIT Number')" />
                            <x-text-input id="ssnit_number" class="block mt-1 w-full" type="text" name="ssnit_number"
                                :value="$user->employeeProfile->ssnit_number" />
                        </div>

                        <div>
                            <x-input-label for="tin" :value="__('Tax ID (TIN)')" />
                            <x-text-input id="tin" class="block mt-1 w-full" type="text" name="tin"
                                :value="$user->employeeProfile->tin" />
                        </div>

                        <!-- Bank Details -->
                        <div class="col-span-2 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Bank Details</h3>
                        </div>

                        <div>
                            <x-input-label for="bank_name" :value="__('Bank Name')" />
                            <x-text-input id="bank_name" class="block mt-1 w-full" type="text" name="bank_name"
                                :value="$user->employeeProfile->bank_name" />
                        </div>

                        <div>
                            <x-input-label for="account_number" :value="__('Account Number')" />
                            <x-text-input id="account_number" class="block mt-1 w-full" type="text"
                                name="account_number" :value="$user->employeeProfile->account_number" />
                        </div>

                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button class="ml-4">
                            {{ __('Save Profile') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>