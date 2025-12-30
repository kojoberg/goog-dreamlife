<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-800 text-center">Welcome Back</h2>
        <p class="text-slate-500 text-center mt-2">Sign in to access your dashboard</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <x-form-input name="email" label="Email Address" type="email" required autofocus autocomplete="username" />

        <!-- Password -->
        <x-form-input name="password" label="Password" type="password" required autocomplete="current-password" />

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-600 hover:text-indigo-800 font-medium" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center py-3 text-lg">
            {{ __('Sign In') }}
        </x-primary-button>
    </form>
</x-guest-layout>