<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dream Life Healthcare | Powered by Uvitech</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap"
        rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .text-gradient {
            background: linear-gradient(to right, #60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .delay-100 {
            animation-delay: 100ms;
        }

        .delay-200 {
            animation-delay: 200ms;
        }

        .delay-300 {
            animation-delay: 300ms;
        }
    </style>
</head>

<body class="bg-black text-white antialiased selection:bg-blue-500 selection:text-white">

    <div class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden">

        <!-- Background Effects -->
        <div class="absolute top-0 left-1/2 w-full -translate-x-1/2 h-full z-0 pointer-events-none">
            <div
                class="absolute top-0 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl mix-blend-screen animate-pulse">
            </div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl mix-blend-screen animate-pulse"
                style="animation-delay: 2s;"></div>
        </div>

        <!-- Navigation -->
        <nav class="absolute top-0 w-full z-20 px-6 py-6 border-b border-white/5 bg-black/50 backdrop-blur-md">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div
                        class="h-8 w-8 bg-gradient-to-tr from-blue-500 to-purple-500 rounded-lg flex items-center justify-center font-bold text-white">
                        D</div>
                    <span class="font-bold text-lg tracking-tight">Dream Life</span>
                </div>
                <div class="flex items-center gap-6">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="text-sm font-medium text-gray-300 hover:text-white transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-5 py-2.5 text-sm font-semibold text-black bg-white rounded-full hover:bg-gray-200 transition-colors shadow-lg shadow-white/10">
                            Employee Login
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="relative z-10 w-full max-w-5xl mx-auto px-6 text-center pt-20">

            <!-- Hero Wrapper -->
            <div class="space-y-8">
                <!-- Badge -->
                <div class="animate-fade-in-up">
                    <span
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-white/10 bg-white/5 text-xs font-medium text-blue-300 backdrop-blur-sm">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                        </span>
                        System Operational
                    </span>
                </div>

                <!-- Headline -->
                <h1
                    class="text-5xl md:text-7xl lg:text-8xl font-extrabold tracking-tight leading-tight animate-fade-in-up delay-100">
                    The Future of <br>
                    <span class="text-gradient">Pharmacy Intelligence</span>
                </h1>

                <!-- Subheadline -->
                <p
                    class="max-w-2xl mx-auto text-lg md:text-xl text-gray-400 leading-relaxed animate-fade-in-up delay-200">
                    Seamlessly integrating clinical safety, inventory precision, and advanced analytics.
                    Empowering healthcare professionals to focus on what matters most.
                </p>

                <!-- Actions -->
                <div
                    class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4 animate-fade-in-up delay-300">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="w-full sm:w-auto px-8 py-4 bg-white text-black font-bold rounded-xl hover:bg-gray-100 transition-all transform hover:scale-105 shadow-xl shadow-white/5">
                            Launch Console
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="w-full sm:w-auto px-8 py-4 bg-white text-black font-bold rounded-xl hover:bg-gray-100 transition-all transform hover:scale-105 shadow-xl shadow-white/5">
                            Access Portal
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Features Grid (Minimal) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-24 text-left animate-fade-in-up delay-300">
                <div
                    class="p-6 rounded-2xl bg-white/5 border border-white/5 hover:border-white/10 transition-colors backdrop-blur-sm group">
                    <div
                        class="h-10 w-10 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">High Velocity POS</h3>
                    <p class="text-sm text-gray-400">Engineered for speed. Process transactions instantly with
                        integrated loyalty checks.</p>
                </div>
                <div
                    class="p-6 rounded-2xl bg-white/5 border border-white/5 hover:border-white/10 transition-colors backdrop-blur-sm group">
                    <div
                        class="h-10 w-10 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400 mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Clinical Safeguards</h3>
                    <p class="text-sm text-gray-400">Real-time interaction screening protects patients automatically at
                        every dispense.</p>
                </div>
                <div
                    class="p-6 rounded-2xl bg-white/5 border border-white/5 hover:border-white/10 transition-colors backdrop-blur-sm group">
                    <div
                        class="h-10 w-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400 mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Predictive Analytics</h3>
                    <p class="text-sm text-gray-400">Smart forecasting models ensure you never overstock or run dry on
                        critical meds.</p>
                </div>
            </div>

        </main>

        <!-- Footer -->
        <footer
            class="w-full mt-auto py-8 text-center border-t border-white/5 bg-black/50 backdrop-blur-md relative z-10">
            <div class="flex flex-col items-center justify-center gap-1">
                <p class="text-xs text-gray-500 tracking-wider uppercase font-medium">Software Developed & Powered By
                </p>
                <div class="flex items-center gap-2 mt-1">
                    <!-- Minimal Logic/Code Icon representation for Uvitech -->
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    <span class="text-lg font-bold text-white tracking-widest">UVITECH</span>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>