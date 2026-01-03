<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Branches') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">All Branches</h3>
                        <a href="{{ route('branches.create') }}"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Add New Branch
                        </a>
                    </div>



                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Name</th>
                                    <th
                                        class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Location</th>
                                    <th
                                        class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Type</th>
                                    <th
                                        class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Users</th>
                                    <th
                                        class="py-2 px-4 border-b text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($branches as $branch)
                                    <tr class="hover:bg-gray-100">
                                        <td class="py-2 px-4 border-b text-sm font-medium text-gray-900">{{ $branch->name }}
                                        </td>
                                        <td class="py-2 px-4 border-b text-sm text-gray-600">{{ $branch->location }}</td>
                                        <td class="py-2 px-4 border-b text-sm">
                                            @if($branch->is_main)
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Main
                                                    HQ</span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Branch</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b text-sm text-gray-600">{{ $branch->users_count }}</td>
                                        <td class="py-2 px-4 border-b text-right text-sm">
                                            <a href="{{ route('branches.edit', $branch) }}"
                                                class="text-indigo-600 hover:text-indigo-900 font-bold mr-3">Edit</a>

                                            @if(!$branch->is_main)
                                                <form action="{{ route('branches.destroy', $branch) }}" method="POST"
                                                    class="inline" onsubmit="return confirm('Are you sure?');">
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