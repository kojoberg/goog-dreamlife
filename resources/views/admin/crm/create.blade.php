<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Campaign') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.crm.store') }}" method="POST">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Campaign Title</label>
                            <input type="text" name="title"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required placeholder="e.g. Monthly Health Tips">
                        </div>

                        <!-- Type -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Channel Type</label>
                            <select name="type"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="sms">SMS (Text Message)</option>
                                <option value="email">Email</option>
                            </select>
                        </div>

                        <!-- Target -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Target Audience</label>
                            <select name="target_role"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="all_patients">All Patients</option>
                                <option value="all_users">All Users (Staff)</option>
                                <option value="pharmacist">Pharmacists Only</option>
                                <option value="cashier">Cashiers Only</option>
                                <option value="admin">Admins Only</option>
                            </select>
                        </div>

                        <!-- Message -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Message Content</label>

                            <!-- Personalization Toggle -->
                            <div class="mb-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_personalized" value="1"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">Personalize Message</span>
                                </label>
                                <p class="text-xs text-gray-500 ml-6">If checked, use <strong>[Name]</strong> in your
                                    message to insert the recipient's name.</p>
                            </div>

                            <textarea name="message" rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required placeholder="Enter you message here..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">For SMS, keep it short (under 160 chars
                                recommended/part).</p>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('admin.crm.index') }}"
                                class="text-blue-500 hover:text-blue-800">Cancel</a>
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Send Campaign
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>