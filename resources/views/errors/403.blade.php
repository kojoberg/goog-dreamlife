<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Forbidden | Dream Life Healthcare</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.bunny.net/css?family=inter:400,600,800&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-900 text-white antialiased h-screen flex items-center justify-center overflow-hidden relative">

    <!-- Background Gradients -->
    <div
        class="absolute top-0 left-1/4 w-96 h-96 bg-red-600/20 rounded-full blur-3xl mix-blend-screen opacity-50 pointer-events-none">
    </div>
    <div
        class="absolute bottom-0 right-1/4 w-96 h-96 bg-orange-600/20 rounded-full blur-3xl mix-blend-screen opacity-50 pointer-events-none">
    </div>

    <div class="relative z-10 text-center px-6">
        <h1
            class="text-9xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-orange-400 mb-4">
            403</h1>
        <h2 class="text-3xl font-bold mb-4">Access Forbidden</h2>
        <p class="text-gray-400 text-lg mb-8 max-w-md mx-auto">You do not have permission to access this page.</p>

        <a href="{{ url('/') }}"
            class="inline-flex items-center justify-center px-8 py-3 text-base font-bold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all shadow-lg shadow-red-900/20">
            Go Home
        </a>
    </div>

</body>

</html>