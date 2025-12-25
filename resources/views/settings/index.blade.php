<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Business Name</label>
                                <input type="text" name="business_name" value="{{ $settings->business_name }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required>
                                <p class="text-xs text-gray-500 mt-1">This will appear on receipts and the dashboard.
                                </p>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Currency Symbol</label>
                                <input type="text" name="currency_symbol" value="{{ $settings->currency_symbol }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                                <input type="text" name="phone" value="{{ $settings->phone }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div class="col-span-1">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                                <input type="email" name="email" value="{{ $settings->email }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                                <textarea name="address" rows="3"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ $settings->address }}</textarea>
                            </div>
                        </div>

                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">SMTP Configuration (Email)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">SMTP Host</label>
                                    <input type="text" name="smtp_host" value="{{ $settings->smtp_host }}"
                                        placeholder="smtp.mailtrap.io"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">SMTP Port</label>
                                    <input type="text" name="smtp_port" value="{{ $settings->smtp_port }}"
                                        placeholder="2525"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                                    <input type="text" name="smtp_username" value="{{ $settings->smtp_username }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                                    <input type="password" name="smtp_password" value="{{ $settings->smtp_password }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">From Address</label>
                                    <input type="email" name="smtp_from_address"
                                        value="{{ $settings->smtp_from_address }}" placeholder="no-reply@dreamlife.com"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">From Name</label>
                                    <input type="text" name="smtp_from_name" value="{{ $settings->smtp_from_name }}"
                                        placeholder="Dream Life Pharmacy"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">SMS Configuration (UelloSend)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">API Key</label>
                                    <input type="text" name="sms_api_key" value="{{ $settings->sms_api_key }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Sender ID</label>
                                    <input type="text" name="sms_sender_id" value="{{ $settings->sms_sender_id }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Loyalty Program Settings</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Spend per Point
                                        (GHS)</label>
                                    <input type="number" step="0.01" name="loyalty_spend_per_point"
                                        value="{{ $settings->loyalty_spend_per_point }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <p class="text-xs text-gray-500 mt-1">Amount customer must spend to earn 1 point.
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Point Value (GHS)</label>
                                    <input type="number" step="0.01" name="loyalty_point_value"
                                        value="{{ $settings->loyalty_point_value }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <p class="text-xs text-gray-500 mt-1">Discount value of 1 point.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Test Configuration</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Test Email</label>
                                    <input type="email" name="test_email_recipient" placeholder="Recipient Email"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Test SMS</label>
                                    <input type="text" name="test_sms_recipient"
                                        placeholder="Recipient Phone (e.g. 0244123456)"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Enter an email or phone number above and click 'Save
                                Settings' to trigger a test message.</p>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Save Settings
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>