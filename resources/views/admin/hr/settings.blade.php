<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('HR Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="{ activeTab: 'general' }">

                    <!-- Tabs -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8">
                            <button @click="activeTab = 'general'"
                                :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'general', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'general' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                General Configuration
                            </button>
                            <button @click="activeTab = 'leave'"
                                :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'leave', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'leave' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Leave Types
                            </button>
                            <button @click="activeTab = 'shifts'"
                                :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'shifts', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'shifts' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Work Shifts
                            </button>
                        </nav>
                    </div>

                    <!-- General Tab -->
                    <div x-show="activeTab === 'general'">
                        <h3 class="text-lg font-medium mb-4">General Configuration</h3>
                        <form action="{{ route('admin.hr.settings.update') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-700">Default Appraisal
                                        Period</label>
                                    <input type="text"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="e.g. 2026-Q1" disabled>
                                    <p class="text-xs text-gray-500 mt-1">Coming soon.</p>
                                </div>
                            </div>
                            <div class="mt-6">
                                <button type="submit"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">Save
                                    Settings</button>
                            </div>
                        </form>
                    </div>

                    <!-- Leave Types Tab -->
                    <div x-show="activeTab === 'leave'" style="display: none;">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Leave Types</h3>
                            <!-- Add Modal Trigger would go here -->
                        </div>

                        <!-- Create Form -->
                        <div class="bg-gray-50 p-4 rounded mb-6 border">
                            <h4 class="text-sm font-bold mb-3">Add New Leave Type</h4>
                            <form action="{{ route('admin.hr.settings.leave-types.store') }}" method="POST"
                                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                @csrf
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-700">Name</label>
                                    <input type="text" name="name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"
                                        placeholder="e.g. Annual Leave" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Days Allowed</label>
                                    <input type="number" name="days_allowed"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"
                                        value="15" required>
                                </div>
                                <div>
                                    <button type="submit"
                                        class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition text-sm">Add</button>
                                </div>
                            </form>
                        </div>

                        <!-- List -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Days Allowed</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Paid</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($leaveTypes as $type)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $type->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $type->days_allowed }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $type->is_paid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $type->is_paid ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No leave
                                                types defined.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Work Shifts Tab -->
                    <div x-show="activeTab === 'shifts'" style="display: none;">
                        <h3 class="text-lg font-medium mb-4">Work Shifts</h3>
                        <!-- Create Form -->
                        <div class="bg-gray-50 p-4 rounded mb-6 border">
                            <h4 class="text-sm font-bold mb-3">Add Work Shift</h4>
                            <form action="{{ route('admin.hr.settings.work-shifts.store') }}" method="POST"
                                class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                @csrf
                                <div class="col-span-1">
                                    <label class="block text-xs font-medium text-gray-700">Shift Name</label>
                                    <input type="text" name="name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"
                                        placeholder="e.g. Regular Day" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Start Time</label>
                                    <input type="time" name="start_time"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">End Time</label>
                                    <input type="time" name="end_time"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"
                                        required>
                                </div>
                                <div>
                                    <button type="submit"
                                        class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition text-sm">Add
                                        Shift</button>
                                </div>
                            </form>
                        </div>

                        <!-- List -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Start</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            End</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($workShifts as $shift)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $shift->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $shift->start_time }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $shift->end_time }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No shifts
                                                defined.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>