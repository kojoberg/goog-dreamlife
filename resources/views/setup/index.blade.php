<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DreamLife - Initial Setup</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">DreamLife Setup</h1>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <p class="mb-4 text-sm text-gray-600">Welcome! It looks like this is a fresh install. Please set up your
                Administrator account and main Branch to get started.</p>

            <form method="POST" action="{{ route('setup.store') }}">
                @csrf

                <!-- Branch Details -->
                <div class="mb-4">
                    <h3 class="font-semibold text-lg text-gray-700 border-b pb-2 mb-3">Branch Details</h3>

                    <div>
                        <x-input-label for="branch_name" :value="__('Branch Name')" />
                        <x-text-input id="branch_name" class="block mt-1 w-full" type="text" name="branch_name"
                            :value="old('branch_name')" required autofocus placeholder="e.g. Main Branch" />
                        <x-input-error :messages="$errors->get('branch_name')" class="mt-2" />
                    </div>

                    <div class="mt-3">
                        <x-input-label for="branch_location" :value="__('Branch Location')" />
                        <x-text-input id="branch_location" class="block mt-1 w-full" type="text" name="branch_location"
                            :value="old('branch_location')" required placeholder="e.g. Accra" />
                        <x-input-error :messages="$errors->get('branch_location')" class="mt-2" />
                    </div>
                </div>

                <!-- Admin Details -->
                <div class="mb-4">
                    <h3 class="font-semibold text-lg text-gray-700 border-b pb-2 mb-3">Administrator Account</h3>

                    <div>
                        <x-input-label for="admin_name" :value="__('Full Name')" />
                        <x-text-input id="admin_name" class="block mt-1 w-full" type="text" name="admin_name"
                            :value="old('admin_name')" required placeholder="e.g. System Admin" />
                        <x-input-error :messages="$errors->get('admin_name')" class="mt-2" />
                    </div>

                    <div class="mt-3">
                        <x-input-label for="admin_email" :value="__('Email Address')" />
                        <x-text-input id="admin_email" class="block mt-1 w-full" type="email" name="admin_email"
                            :value="old('admin_email')" required placeholder="admin@example.com" />
                        <x-input-error :messages="$errors->get('admin_email')" class="mt-2" />
                    </div>

                    <div class="mt-3">
                        <x-input-label for="admin_password" :value="__('Password')" />
                        <x-text-input id="admin_password" class="block mt-1 w-full" type="password"
                            name="admin_password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('admin_password')" class="mt-2" />
                    </div>

                    <div class="mt-3">
                        <x-input-label for="admin_password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="admin_password_confirmation" class="block mt-1 w-full" type="password"
                            name="admin_password_confirmation" required />
                        <x-input-error :messages="$errors->get('admin_password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button class="ml-4">
                        {{ __('Complete Setup') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>