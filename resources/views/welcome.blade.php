<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dream Life Healthcare | Powered by Uvitech</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Fallback for if Tailwind fails to load immediately */
        body {
            background-color: #111827;
            color: white;
            font-family: 'Inter', sans-serif;
            margin: 0;
        }

        .min-h-screen {
            min-height: 100vh;
        }

        .flex {
            display: flex;
        }

        .flex-col {
            flex-direction: column;
        }

        .items-center {
            align-items: center;
        }

        .justify-center {
            justify-content: center;
        }

        .text-center {
            text-align: center;
        }

        /* Custom Utilities */
        .glass-panel {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="bg-gray-900 text-white antialiased">

    <div class="min-h-screen flex flex-col relative overflow-hidden">

        <!-- Navbar -->
        <nav class="w-full z-50 glass-panel border-b-0">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <!-- Logo / Brand -->
                    <div class="flex-shrink-0 flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                            <span class="text-xl font-bold text-white">D</span>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-white">Dream Life</span>
                    </div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center gap-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="px-5 py-2.5 rounded-lg bg-gray-800 hover:bg-gray-700 text-white text-sm font-semibold transition-all border border-gray-700">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="px-6 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold shadow-lg shadow-blue-600/20 transition-all transform hover:scale-105">
                                    Log In
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Hero -->
        <main class="flex-grow flex flex-col relative">
            <!-- Background Gradients -->
            <div
                class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl mix-blend-screen opacity-50 pointer-events-none">
            </div>
            <div
                class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl mix-blend-screen opacity-50 pointer-events-none">
            </div>

            <!-- Hero Section -->
            <div class="w-full min-h-[calc(100vh-5rem)] flex items-center justify-center relative z-10 pb-20">
                <div class="relative max-w-4xl mx-auto px-6 text-center">

                    <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6 leading-tight">
                        UVITECH RxPMS. <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">
                            Pharmacy Management System.
                        </span>
                    </h1>

                    <p class="mt-4 text-xl text-gray-400 max-w-2xl mx-auto mb-10">
                        UVITECH RxPMS is the next-generation Pharmacy Management System. Secure, efficient, and
                        reliable.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all shadow-xl shadow-blue-900/20">
                                Open Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all shadow-xl shadow-blue-900/20">
                                Staff Login Portal
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <section class="relative z-10 py-24 bg-gray-900/50 backdrop-blur-sm border-t border-gray-800">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-bold text-white sm:text-4xl">Powerful Features for Modern Pharmacies
                        </h2>
                        <p class="mt-4 text-lg text-gray-400">Everything you need to run your pharmacy efficiently.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Feature 1: POS -->
                        <div class="glass-panel p-8 rounded-2xl hover:bg-gray-800/50 transition-colors">
                            <div class="w-12 h-12 bg-blue-500/10 rounded-lg flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-3">Smart Point of Sale</h3>
                            <p class="text-gray-400 leading-relaxed">Fast and intuitive checkout process with barcode
                                scanning, receipt printing, and automated tax calculations.</p>
                        </div>

                        <!-- Feature 2: Inventory -->
                        <div class="glass-panel p-8 rounded-2xl hover:bg-gray-800/50 transition-colors">
                            <div class="w-12 h-12 bg-purple-500/10 rounded-lg flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-3">Inventory Command</h3>
                            <p class="text-gray-400 leading-relaxed">Real-time stock tracking with expiry alerts, low
                                stock
                                notifications, and batch management.</p>
                        </div>

                        <!-- Feature 3: Analytics -->
                        <div class="glass-panel p-8 rounded-2xl hover:bg-gray-800/50 transition-colors">
                            <div class="w-12 h-12 bg-pink-500/10 rounded-lg flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-pink-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-3">Advanced Analytics</h3>
                            <p class="text-gray-400 leading-relaxed">Gain insights with detailed sales reports, profit
                                margins, and staff performance metrics.</p>
                        </div>

                        <!-- Feature 4: Procurement -->
                        <div class="glass-panel p-8 rounded-2xl hover:bg-gray-800/50 transition-colors">
                            <div class="w-12 h-12 bg-emerald-500/10 rounded-lg flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-3">Procurement & Suppliers</h3>
                            <p class="text-gray-400 leading-relaxed">Manage suppliers, track purchase orders, and
                                automate
                                restocking to ensure you never run out.</p>
                        </div>

                        <!-- Feature 5: Security -->
                        <div class="glass-panel p-8 rounded-2xl hover:bg-gray-800/50 transition-colors">
                            <div class="w-12 h-12 bg-orange-500/10 rounded-lg flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-orange-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-3">Role-Based Security</h3>
                            <p class="text-gray-400 leading-relaxed">Secure access controls for Administrators,
                                Pharmacists,
                                and Cashiers.</p>
                        </div>

                        <!-- Feature 6: System Health -->
                        <div class="glass-panel p-8 rounded-2xl hover:bg-gray-800/50 transition-colors">
                            <div class="w-12 h-12 bg-teal-500/10 rounded-lg flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-teal-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white mb-3">System Health</h3>
                            <p class="text-gray-400 leading-relaxed">Built-in monitoring tools for database
                                connectivity,
                                disk space, and backup integrity.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Branding Footer -->
        <footer class="w-full py-8 text-center relative z-10 border-t border-gray-800 bg-gray-900/50 backdrop-blur">
            <div class="flex flex-col items-center justify-center gap-2">
                <span class="text-xs uppercase tracking-widest text-gray-500 font-semibold">Software Developed & Powered
                    By</span>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                        </path>
                    </svg>
                    <span class="text-xl font-extrabold text-white tracking-wider"><a href="https://uvitechgh.com"
                            class="text-blue-500 hover:text-blue-600">UVITECH, Inc.</a></span>
                </div>
                <!-- Software Version -->
                <div class="mt-2">
                    <span class="px-2 py-0.5 rounded text-xs text-gray-400 bg-gray-800/50 border border-gray-700">
                        v{{ $gitVersion ?? '1.0.0' }}
                    </span>
                </div>
            </div>
        </footer>

    </div>
</body>

</html>