<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-4xl font-bold text-blue-600">{{ $usersCount }}</div>
                    <div class="text-gray-500 font-semibold">Total Users</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-4xl font-bold text-orange-600">{{ $branchesCount }}</div>
                    <div class="text-gray-500 font-semibold">Active Branches</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-4xl font-bold text-red-600">{{ $lowStockCount }}</div>
                    <div class="text-gray-500 font-semibold">Low Stock Events</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-green-600">GHS {{ number_format($todaySales, 2) }}</div>
                    <div class="text-gray-500 font-semibold">Today's Sales (Global)</div>
                </div>
            </div>

            <!-- Management Tools -->
            <h3 class="text-lg font-bold mb-4 text-gray-700">Management Tools</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Users -->
                <a href="{{ route('users.index') }}"
                    class="block bg-white shadow hover:shadow-lg rounded-lg p-6 transition">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full text-blue-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">User Management</h4>
                            <p class="text-sm text-gray-500">Add, edit, or remove system users.</p>
                        </div>
                    </div>
                </a>

                <!-- Branches -->
                <a href="{{ route('branches.index') }}"
                    class="block bg-white shadow hover:shadow-lg rounded-lg p-6 transition">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full text-purple-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">Branch Management</h4>
                            <p class="text-sm text-gray-500">Configure multi-branch settings.</p>
                        </div>
                    </div>
                </a>

                <!-- Settings -->
                <a href="{{ route('settings.index') }}"
                    class="block bg-white shadow hover:shadow-lg rounded-lg p-6 transition">
                    <div class="flex items-center">
                        <div class="bg-gray-100 p-3 rounded-full text-gray-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">System Settings</h4>
                            <p class="text-sm text-gray-500">SMTP, SMS, Logo, Tax Config.</p>
                        </div>
                    </div>
                </a>

                <!-- Audit Logs -->
                <a href="{{ route('audit-logs.index') }}"
                    class="block bg-white shadow hover:shadow-lg rounded-lg p-6 transition">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-full text-yellow-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">Audit Logs</h4>
                            <p class="text-sm text-gray-500">View system activity trails.</p>
                        </div>
                    </div>
                </a>

                <!-- Backups -->
                <a href="{{ route('backups.index') }}"
                    class="block bg-white shadow hover:shadow-lg rounded-lg p-6 transition">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full text-green-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">Backups</h4>
                            <p class="text-sm text-gray-500">Manage database backups.</p>
                        </div>
                    </div>
                </a>

                <!-- Expenses -->
                <a href="{{ route('expenses.index') }}"
                    class="block bg-white shadow hover:shadow-lg rounded-lg p-6 transition">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-3 rounded-full text-red-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">Expenses</h4>
                            <p class="text-sm text-gray-500">Track company expenditures.</p>
                        </div>
                    </div>
                </a>

                <!-- Analytics -->
                <a href="{{ route('analytics.index') }}"
                    class="block bg-white shadow hover:shadow-lg rounded-lg p-6 transition">
                    <div class="flex items-center">
                        <div class="bg-indigo-100 p-3 rounded-full text-indigo-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">Analytics</h4>
                            <p class="text-sm text-gray-500">Detailed sales & performance reports.</p>
                        </div>
                    </div>
                </a>

                <!-- Support -->
                <a href="{{ route('support.index') }}"
                    class="block bg-white shadow hover:shadow-lg rounded-lg p-6 transition">
                    <div class="flex items-center">
                        <div class="bg-teal-100 p-3 rounded-full text-teal-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">Support / Help</h4>
                            <p class="text-sm text-gray-500">System information and assistance.</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>