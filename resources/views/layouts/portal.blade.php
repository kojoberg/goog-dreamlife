<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $settings = \App\Models\Setting::first();
        $pharmacyName = $settings->business_name ?? config('app.name', 'Pharmacy');
    @endphp

    <title>{{ $pharmacyName }} - Patient Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-indigo-600 border-b border-indigo-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('portal.dashboard') }}"
                                class="flex items-center text-white font-bold text-lg">
                                @if($settings && $settings->logo_path)
                                    @php
                                        $logoPath = storage_path('app/public/' . $settings->logo_path);
                                    @endphp
                                    @if(file_exists($logoPath))
                                        <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo"
                                            class="h-10 w-auto mr-3">
                                    @endif
                                @endif
                                <span>{{ $pharmacyName }} Portal</span>
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('portal.dashboard') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('portal.dashboard') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Dashboard
                            </a>
                            <a href="{{ route('portal.transactions') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('portal.transactions') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Transactions
                            </a>
                            <a href="{{ route('portal.loyalty') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('portal.loyalty') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Loyalty Points
                            </a>
                            <a href="{{ route('portal.history') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('portal.history') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Prescriptions
                            </a>
                            <a href="{{ route('portal.upload') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('portal.upload') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Upload Prescription
                            </a>
                            <a href="{{ route('portal.documents') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('portal.documents') ? 'border-white text-white' : 'border-transparent text-indigo-200 hover:text-white hover:border-indigo-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                Documents
                            </a>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open"
                                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-white transition">
                                    <span class="text-white font-medium">{{ Auth::user()->name }}</span>
                                    <svg class="ml-2 -mr-0.5 h-4 w-4 text-indigo-200" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <div x-show="open" @click.away="open = false" style="display: none;"
                                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="flex items-center sm:hidden">
                        <button x-data="{ mobileOpen: false }" @click="mobileOpen = !mobileOpen"
                            class="inline-flex items-center justify-center p-2 rounded-md text-indigo-200 hover:text-white hover:bg-indigo-500 focus:outline-none focus:bg-indigo-500 focus:text-white transition">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>

</html>