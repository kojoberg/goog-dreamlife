<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Name -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
                                <input
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="name" type="text" name="name" value="{{ $user->name }}" required>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                                <input
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="email" type="email" name="email" value="{{ $user->email }}" required>
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="role">Role</label>
                                <select
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="role" name="role" required>
                                    <option value="cashier" {{ $user->role == 'cashier' ? 'selected' : '' }}>Cashier
                                    </option>
                                    <option value="pharmacist" {{ $user->role == 'pharmacist' ? 'selected' : '' }}>
                                        Pharmacist</option>
                                    <option value="doctor" {{ $user->role == 'doctor' ? 'selected' : '' }}>Doctor</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator
                                    </option>
                                </select>
                            </div>

                            <!-- Branch Assignment -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="branch_id">Assign
                                    Branch</label>
                                <select
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="branch_id" name="branch_id" required>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ $user->branch_id == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }} {{ $branch->is_main ? '(Main HQ)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Password (Optional) -->
                            <div class="col-span-2 border-t pt-4 mt-2">
                                <h3 class="text-sm font-bold text-gray-700 mb-2">Change Password (Leave blank to keep
                                    current)</h3>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">New
                                    Password</label>
                                <input
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="password" type="password" name="password" autocomplete="new-password">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2"
                                    for="password_confirmation">Confirm New Password</label>
                                <input
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="password_confirmation" type="password" name="password_confirmation">
                            </div>

                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a class="font-bold text-sm text-gray-600 hover:text-gray-900 mr-4"
                                href="{{ route('users.index') }}">
                                Cancel
                            </a>
                            <button
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                type="submit">
                                Update User
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>