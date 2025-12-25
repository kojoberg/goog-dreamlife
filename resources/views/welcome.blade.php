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
        <main class="flex-grow flex items-center justify-center relative">
            <!-- Background Gradients -->
            <div
                class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl mix-blend-screen opacity-50 pointer-events-none">
            </div>
            <div
                class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl mix-blend-screen opacity-50 pointer-events-none">
            </div>

            <div class="relative max-w-4xl mx-auto px-6 text-center z-10 py-12">

                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6 leading-tight">
                    Precision Care. <br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">
                        Intelligent Systems.
                    </span>
                </h1>

                <p class="mt-4 text-xl text-gray-400 max-w-2xl mx-auto mb-10">
                    The next-generation Pharmacy Management System. Secure, efficient, and reliable.
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
                    <span class="text-xl font-extrabold text-white tracking-wider">UVITECH</span>
                </div>
            </div>
        </footer>

    </div>
</body>

</html>