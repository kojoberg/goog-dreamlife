<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dream Life Healthcare</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased font-sans bg-gray-50 text-gray-800">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full z-10 transition-all duration-300" x-data="{ scrolled: false }"
        @scroll.window="scrolled = (window.pageYOffset > 20)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center">
                    <x-application-logo class="block h-10 w-auto fill-current text-blue-600" />
                    <span class="ml-3 text-xl font-bold tracking-tight text-gray-900">Dream Life <span
                            class="text-blue-600">Healthcare</span></span>
                </div>
                <div class="hidden sm:flex items-center space-x-6">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-sm font-semibold text-gray-700 hover:text-blue-600">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-blue-600">Log
                                in</a>
                        @endauth
                    @endif
                </div>
                <!-- Mobile Menu Button (Hamburger) - Simplified for this landing -->
                <div class="sm:hidden flex items-center">
                    <a href="{{ route('login') }}" class="text-sm font-bold text-blue-600">Log In</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-white pt-20 pb-16 overflow-hidden sm:pt-24 sm:pb-20 lg:pb-28">
        <div class="absolute inset-0 overflow-hidden">
            <!-- Abstract Background Gradient -->
            <div class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-l from-blue-50 to-white opacity-50"></div>
            <svg class="absolute top-0 right-0 -mr-20 -mt-20 hidden lg:block" width="404" height="384" fill="none"
                viewBox="0 0 404 384" aria-hidden="true">
                <defs>
                    <pattern id="de316486-4a29-4312-bdfc-fbce2132a2c1" x="0" y="0" width="20" height="20"
                        patternUnits="userSpaceOnUse">
                        <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor" />
                    </pattern>
                </defs>
                <rect width="404" height="384" fill="url(#de316486-4a29-4312-bdfc-fbce2132a2c1)" />
            </svg>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block text-gray-900">Advanced Care.</span>
                        <span class="block text-blue-600">Seamless Operation.</span>
                    </h1>
                    <p
                        class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Managing healthcare should be as precise as the medicine itself. We provide an integrated
                        Pharmacy Management System designed for efficiency, safety, and growth.
                    </p>
                    <div class="mt-8 sm:max-w-lg sm:mx-auto sm:text-center lg:text-left lg:mx-0">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition duration-150 ease-in-out">
                                Go to Dashboard &rarr;
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition duration-150 ease-in-out">
                                Staff Login
                            </a>
                        @endauth
                    </div>
                </div>
                <div
                    class="mt-12 relative sm:max-w-lg sm:mx-auto lg:mt-0 lg:max-w-none lg:mx-0 lg:col-span-6 lg:flex lg:items-center">
                    <div
                        class="relative mx-auto w-full rounded-lg shadow-lg lg:max-w-md overflow-hidden transform hover:scale-105 transition duration-500">
                        <div class="relative block w-full bg-white rounded-lg overflow-hidden">
                            <img class="w-full"
                                src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80"
                                alt="Pharmacy shelves">
                            <div class="absolute inset-0 bg-gray-500 mix-blend-multiply opacity-20"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-16 bg-gray-50 overflow-hidden lg:py-24">
        <div class="relative max-w-xl mx-auto px-4 sm:px-6 lg:px-8 lg:max-w-7xl">
            <div class="relative text-center">
                <h2 class="text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Enterprise-Grade Features
                </h2>
                <p class="mt-4 max-w-3xl mx-auto text-center text-xl text-gray-500">
                    Everything you need to run a modern pharmacy and clinic, from inventory to patient care.
                </p>
            </div>

            <div class="relative mt-12 lg:mt-24 lg:grid lg:grid-cols-3 lg:gap-8">
                <!-- Feature 1 -->
                <div class="hover:shadow-xl p-6 rounded-2xl bg-white transition duration-300">
                    <div class="inline-flex items-center justify-center p-3 bg-blue-500 rounded-md shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div class="mt-5">
                        <h3 class="text-lg font-medium text-gray-900">Inventory & POS</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Real-time stock tracking with FIFO batch management. Fast point of sale with integrated
                            loyalty and tax calculations.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="mt-10 lg:mt-0 hover:shadow-xl p-6 rounded-2xl bg-white transition duration-300">
                    <div class="inline-flex items-center justify-center p-3 bg-green-500 rounded-md shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-5">
                        <h3 class="text-lg font-medium text-gray-900">Clinical Safety</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Automatic drug interaction checks at the point of sale. Protect your patients with built-in
                            safety rules and alerts.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="mt-10 lg:mt-0 hover:shadow-xl p-6 rounded-2xl bg-white transition duration-300">
                    <div class="inline-flex items-center justify-center p-3 bg-purple-500 rounded-md shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="mt-5">
                        <h3 class="text-lg font-medium text-gray-900">Analytics & Insights</h3>
                        <p class="mt-2 text-base text-gray-500">
                            Data-driven decision making. ABC Analysis, Sales Forecasting, and Profit Reports to optimize
                            your business performance.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
            <div class="flex justify-center space-x-6 md:order-2">
                <span class="text-gray-400 hover:text-gray-500">
                    Trusted by Pharmacists across Ghana.
                </span>
            </div>
            <div class="mt-8 md:mt-0 md:order-1">
                <p class="text-center text-base text-gray-400">
                    &copy; {{ date('Y') }} Dream Life Healthcare. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

</body>

</html>