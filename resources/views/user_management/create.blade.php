<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Name -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
                                <input
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="name" type="text" name="name" required>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                                <input
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="email" type="email" name="email" required>
                            </div>

                            <!-- Role -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="role">Role</label>
                                <select
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="role" name="role" required>
                                    <option value="cashier">Cashier</option>
                                    <option value="pharmacist">Pharmacist</option>
                                    <option value="doctor">Doctor</option>
                                    <option value="admin">Administrator</option>
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
                                        <option value="{{ $branch->id }}">{{ $branch->name }}
                                            {{ $branch->is_main ? '(Main HQ)' : '' }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">User will primarily see data for this branch.</p>
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2"
                                    for="password">Password</label>
                                <input
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="password" type="password" name="password" required autocomplete="new-password">
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2"
                                    for="password_confirmation">Confirm Password</label>
                                <input
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="password_confirmation" type="password" name="password_confirmation" required>
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
                                Create User
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>