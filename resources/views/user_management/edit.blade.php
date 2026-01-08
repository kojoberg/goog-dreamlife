<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-10">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Edit User</h1>
                <p class="text-slate-500 text-sm mt-1">Update user details and permissions.</p>
            </div>
            <a href="{{ route('users.index') }}"
                class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold py-2 px-4 rounded-lg shadow-sm transition">
                &larr; Back to List
            </a>
        </div>

        <x-card class="max-w-4xl mx-auto">
            <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form-input name="name" label="Full Name" :value="$user->name" required />

                    <x-form-input name="email" label="Email Address" type="email" :value="$user->email" required />

                    <x-form-select name="role" label="Role" required>
                        <option value="cashier" {{ $user->role == 'cashier' ? 'selected' : '' }}>Cashier</option>
                        <option value="pharmacist" {{ $user->role == 'pharmacist' ? 'selected' : '' }}>Pharmacist</option>
                        <option value="doctor" {{ $user->role == 'doctor' ? 'selected' : '' }}>Doctor</option>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </x-form-select>

                    <x-form-select name="branch_id" label="Branch" required>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $user->branch_id == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }} {{ $branch->is_main ? '(Main HQ)' : '' }}
                            </option>
                        @endforeach
                    </x-form-select>

                    <div class="col-span-1 md:col-span-2 border-t pt-4 mt-2">
                        <h3 class="text-sm font-bold text-slate-700 mb-4">Permissions</h3>
                        <p class="text-xs text-gray-500 mb-2">Assign specific permissions to this user. (Admins have all
                            permissions by default).</p>
                        <input type="hidden" name="permissions_submitted" value="1">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($permissions as $permission)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        {{ $user->permissions->contains($permission->id) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-span-1 md:col-span-2 border-t pt-4 mt-2">
                        <h3 class="text-sm font-bold text-slate-700 mb-4">Change Password (Optional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-form-input name="password" label="New Password" type="password"
                                autocomplete="new-password" helper="Leave blank to keep current password" />

                            <x-form-input name="password_confirmation" label="Confirm New Password" type="password" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <x-primary-button>
                        Update User Account
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>