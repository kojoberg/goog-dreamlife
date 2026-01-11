<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Health Status') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- System Versions -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h4 class="text-xs font-bold text-blue-500 uppercase">PHP Version</h4>
                            <p class="text-lg font-bold text-gray-800">{{ $systemInfo['php'] }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <h4 class="text-xs font-bold text-red-500 uppercase">Laravel Version</h4>
                            <p class="text-lg font-bold text-gray-800">v{{ $systemInfo['laravel'] }}</p>
                        </div>
                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                            <h4 class="text-xs font-bold text-indigo-500 uppercase">Database</h4>
                            <p class="text-lg font-bold text-gray-800">{{ Str::limit($systemInfo['database'], 15) }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <h4 class="text-xs font-bold text-purple-500 uppercase">App Version</h4>
                            <p class="text-lg font-bold text-gray-800">{{ $systemInfo['app_version'] }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($checks as $key => $check)
                            @php
                                $color = match ($check['status']) {
                                    'ok' => 'bg-green-100 border-green-500 text-green-700',
                                    'warning' => 'bg-yellow-100 border-yellow-500 text-yellow-700',
                                    'error' => 'bg-red-100 border-red-500 text-red-700',
                                    default => 'bg-gray-100 border-gray-500 text-gray-700'
                                };
                                $icon = match ($check['status']) {
                                    'ok' => '✔',
                                    'warning' => '⚠',
                                    'error' => '✖',
                                    default => '?'
                                };
                                // Custom title mappings for better display
                                $titleMappings = [
                                    'uello_sms' => 'UelloSend SMS',
                                    'rxnav' => 'RxNav API',
                                    'google_drive' => 'Google Drive Backup',
                                ];
                                $title = $titleMappings[$key] ?? ucwords(str_replace('_', ' ', $key));
                            @endphp

                            <div class="border-l-4 p-4 rounded {{ $color }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-bold text-lg mb-1">{{ $title }}</h3>
                                        <p class="text-sm font-medium">{{ $check['message'] }}</p>
                                    </div>
                                    <div class="text-2xl">{{ $icon }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 border-t pt-4">
                        <h3 class="text-lg font-bold mb-2">Check Details</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            These checks run in real-time when you load this page. If "External APIs" are red, check
                            your internet connection and verify configurations in Settings.
                        </p>

                        <div class="flex items-center justify-between bg-gray-100 p-4 rounded-lg">
                            <div>
                                <h4 class="font-bold text-gray-800">Developer Options</h4>
                                <p class="text-sm text-gray-600">Enable detailed error reporting. <span
                                        class="text-red-600 font-bold">Use with caution.</span></p>
                            </div>
                            <form action="{{ route('admin.system-health.toggle-debug') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2 rounded font-bold text-sm {{ $checks['debug_mode']['val'] ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-gray-300 hover:bg-gray-400 text-gray-800' }}">
                                    {{ $checks['debug_mode']['val'] ? 'Disable Debug Mode' : 'Enable Debug Mode' }}
                                </button>
                            </form>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('admin.system-health') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Refresh Status
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
</x-app-layout>