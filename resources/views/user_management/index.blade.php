<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">All Users</h3>
                        <a href="{{ route('users.create') }}"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Add New User
                        </a>
                    </div>



                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Name</th>
                                    <th
                                        class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Role</th>
                                    <th
                                        class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Assigned Branch</th>
                                    <th
                                        class="py-2 px-4 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-100">
                                        <td class="py-2 px-4 border-b text-sm font-medium text-gray-900">{{ $user->name }}
                                        </td>
                                        <td class="py-2 px-4 border-b text-sm text-gray-600">{{ $user->email }}</td>
                                        <td class="py-2 px-4 border-b text-sm">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b text-sm text-gray-600">
                                            @if($user->branch)
                                                <span class="font-bold text-indigo-600">{{ $user->branch->name }}</span>
                                            @else
                                                <span class="text-gray-400 italic">No Branch</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b text-right text-sm">
                                            <a href="{{ route('users.edit', $user) }}"
                                                class="text-indigo-600 hover:text-indigo-900 font-bold mr-3">Edit</a>

                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>