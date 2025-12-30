<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-10">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Add New User</h1>
                <p class="text-slate-500 text-sm mt-1">Create a user account and assign roles.</p>
            </div>
            <a href="{{ route('users.index') }}"
                class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold py-2 px-4 rounded-lg shadow-sm transition">
                &larr; Back to List
            </a>
        </div>

        <x-card class="max-w-4xl mx-auto">
            <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form-input name="name" label="Full Name" placeholder="e.g. Samuel Osei" required />

                    <x-form-input name="email" label="Email Address" type="email" required />

                    <x-form-select name="role" label="Role" required>
                        <option value="cashier">Cashier</option>
                        <option value="pharmacist">Pharmacist</option>
                        <option value="doctor">Doctor</option>
                        <option value="admin">Administrator</option>
                    </x-form-select>

                    <div>
                        <x-form-select name="branch_id" label="Assign Branch" required>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}
                                    {{ $branch->is_main ? '(Main HQ)' : '' }}</option>
                            @endforeach
                        </x-form-select>
                        <p class="text-xs text-slate-500 mt-1">User will primarily see data for this branch.</p>
                    </div>

                    <x-form-input name="password" label="Password" type="password" required
                        autocomplete="new-password" />

                    <x-form-input name="password_confirmation" label="Confirm Password" type="password" required />
                </div>

                <div class="flex justify-end pt-2">
                    <x-primary-button>
                        Create User Account
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>