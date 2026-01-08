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



                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
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
                                <label class="block text-gray-700 text-sm font-bold mb-2">TIN Number</label>
                                <input type="text" name="tin_number" value="{{ $settings->tin_number }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <p class="text-xs text-gray-500 mt-1">Tax Identification Number.</p>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Receipt Logo</label>
                                <input type="file" name="logo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @if($settings->logo_path)
                                    <p class="text-xs text-green-500 mt-1">Current logo uploaded.</p>
                                @endif
                            </div>

                            <div class="col-span-1">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Currency Symbol</label>
                                <input type="text" name="currency_symbol" value="{{ $settings->currency_symbol }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Expiry Alert (Days)</label>
                                <input type="number" name="alert_expiry_days"
                                    value="{{ $settings->alert_expiry_days ?? 90 }}"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required min="1">
                                <p class="text-xs text-gray-500 mt-1">Days before expiry to trigger alert.</p>
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

                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                                <textarea name="address" rows="3"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ $settings->address }}</textarea>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Font Family</label>
                                <select name="font_family" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="Segoe UI" {{ $settings->font_family == 'Segoe UI' ? 'selected' : '' }}>Segoe UI (Default)</option>
                                    <option value="Inter" {{ $settings->font_family == 'Inter' ? 'selected' : '' }}>Inter</option>
                                    <option value="Roboto" {{ $settings->font_family == 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                    <option value="Open Sans" {{ $settings->font_family == 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                                    <option value="Lato" {{ $settings->font_family == 'Lato' ? 'selected' : '' }}>Lato</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">System-wide font preference.</p>
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
                                        placeholder="UVITECH Healthcare"
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
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Google Drive Backup Configuration</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Client ID</label>
                                    <input type="text" name="google_drive_client_id" value="{{ $settings->google_drive_client_id }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Client Secret</label>
                                    <input type="password" name="google_drive_client_secret" value="{{ $settings->google_drive_client_secret }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Refresh Token</label>
                                    <input type="text" name="google_drive_refresh_token" value="{{ $settings->google_drive_refresh_token }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <p class="text-xs text-gray-500 mt-1">
                                        Use the <a href="https://developers.google.com/oauthplayground" target="_blank" class="text-blue-600 underline">Google OAuth Playground</a> to generate a refresh token with `https://www.googleapis.com/auth/drive` scope.
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Folder ID (Optional)</label>
                                    <input type="text" name="google_drive_folder_id" value="{{ $settings->google_drive_folder_id }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <p class="text-xs text-gray-500 mt-1">ID of the folder to store backups in.</p>
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
                            <h3 class="text-lg font-medium text-gray-900 mb-4">System Preferences</h3>
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input id="enable_tax" name="enable_tax" type="checkbox"
                                        {{ $settings->enable_tax ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="enable_tax" class="ml-2 block text-sm text-gray-900">
                                        Enable Tax Calculation in POS
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Refund Policy Settings -->
                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Refund Policy</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Refund Limit (Days)</label>
                                    <input type="number" name="refund_policy_days" value="{{ $settings->refund_policy_days ?? 7 }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <p class="text-xs text-gray-500 mt-1">Number of days after purchase that refunds are allowed. Set to 0 to disable.</p>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Policy Text (Receipt Footer)</label>
                                    <textarea name="refund_policy_text" rows="3"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        placeholder="E.g., Goods sold in good condition cannot be returned.">{{ $settings->refund_policy_text }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Notifications</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-bold text-sm text-gray-700 mb-2">Low Stock Alerts</h4>
                                    <div class="flex items-center mb-2">
                                        <input id="notify_low_stock_email" name="notify_low_stock_email" type="checkbox"
                                            {{ $settings->notify_low_stock_email ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="notify_low_stock_email" class="ml-2 block text-sm text-gray-900">Email</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="notify_low_stock_sms" name="notify_low_stock_sms" type="checkbox"
                                            {{ $settings->notify_low_stock_sms ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="notify_low_stock_sms" class="ml-2 block text-sm text-gray-900">SMS</label>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm text-gray-700 mb-2">Expiry Alerts</h4>
                                    <div class="flex items-center mb-2">
                                        <input id="notify_expiry_email" name="notify_expiry_email" type="checkbox"
                                            {{ $settings->notify_expiry_email ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="notify_expiry_email" class="ml-2 block text-sm text-gray-900">Email</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="notify_expiry_sms" name="notify_expiry_sms" type="checkbox"
                                            {{ $settings->notify_expiry_sms ? 'checked' : '' }}
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="notify_expiry_sms" class="ml-2 block text-sm text-gray-900">SMS</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Administration (License & Version) -->
                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">System Administration</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg">
                                
                                <!-- Version Info -->
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Software Version</label>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-900 font-mono text-sm">{{ $systemVersion }}</span>
                                        <button id="btn-check-updates" type="button" class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200">
                                            Check for Updates
                                        </button>
                                    </div>
                                </div>

                                <!-- License Info -->
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">License Status</label>
                                     @if($settings->license_expiry)
                                        @php
                                            $expiry = \Carbon\Carbon::parse($settings->license_expiry);
                                            $isExpired = $expiry->isPast();
                                        @endphp
                                        <div class="text-sm {{ $isExpired ? 'text-red-600 font-bold' : 'text-green-600 font-bold' }}">
                                            {{ $isExpired ? 'EXPIRED' : 'ACTIVE' }} (Expires: {{ $expiry->format('d M Y') }})
                                        </div>
                                     @else
                                        @php
                                            // 14-Day Trial Logic
                                            $installDate = $settings->created_at ?? \Carbon\Carbon::now()->subDays(1); // Fallback if null
                                            $trialEndsAt = $installDate->copy()->addDays(14);
                                            $daysRemaining = round(now()->floatDiffInDays($trialEndsAt, false)); 
                                            $isTrialActive = $daysRemaining >= 0;
                                        @endphp
                                        
                                        @if($isTrialActive)
                                            <div class="text-sm text-orange-500 font-bold">
                                                TRIAL MODE (Active)
                                                <span class="block text-xs font-normal text-gray-600 mt-1">
                                                    {{ $daysRemaining }} Day(s) Remaining. Limit usage to testing.
                                                </span>
                                            </div>
                                        @else
                                            <div class="text-sm text-red-600 font-bold">
                                                TRIAL EXPIRED
                                                <span class="block text-xs font-normal text-gray-600 mt-1">
                                                    Your 14-day trial has ended. Please activate a license.
                                                </span>
                                            </div>
                                        @endif
                                     @endif
                                </div>

                                <!-- License Key Input -->
                                <div class="md:col-span-2">
                                     <label class="block text-gray-700 text-sm font-bold mb-2">Update License Key</label>
                                     <input type="text" name="license_key" value="{{ $settings->license_key }}" placeholder="Enter new license key..."
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                     <p class="text-xs text-gray-500 mt-1">Contact support to renew or purchase a license.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Branch Management -->
                        <div class="mt-8 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Multi-Branch Operations</h3>
                            <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center">
                                <div>
                                    <h4 class="font-bold text-gray-800">Manage Branches</h4>
                                    <p class="text-sm text-gray-600">Create and manage multiple pharmacy branches.</p>
                                </div>
                                <a href="{{ route('branches.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Manage Branches
                                </a>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center mt-4">
                                <div>
                                    <h4 class="font-bold text-gray-800">User Management</h4>
                                    <p class="text-sm text-gray-600">Add staff, assign roles and branches.</p>
                                </div>
                                <a href="{{ route('users.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Manage Users
                                </a>
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

                    <!-- Update Modal (Hidden by default) -->
                    <div id="update-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                            <div class="mt-3 text-center">
                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100">
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2" id="modal-title">Checking for Updates...</h3>
                                <div class="mt-2 px-7 py-3">
                                    <p class="text-sm text-gray-500" id="modal-content">
                                        Please wait while we check for the latest software version.
                                    </p>
                                    <pre id="modal-commits" class="text-left text-xs bg-gray-100 p-2 mt-2 rounded hidden overflow-x-auto"></pre>
                                </div>
                                <div class="items-center px-4 py-3">
                                    <button id="btn-close-modal" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                        Close
                                    </button>
                                    <form id="execute-update-form" action="{{ route('settings.system_update') }}" method="POST" class="hidden mt-2">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            Update Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const checkBtn = document.getElementById('btn-check-updates');
                            const modal = document.getElementById('update-modal');
                            const modalTitle = document.getElementById('modal-title');
                            const modalContent = document.getElementById('modal-content');
                            const modalCommits = document.getElementById('modal-commits');
                            const btnClose = document.getElementById('btn-close-modal');
                            const formUpdate = document.getElementById('execute-update-form');

                            // Open Modal & Check
                            checkBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                modal.classList.remove('hidden');
                                modalTitle.textContent = "Checking for Updates...";
                                modalContent.textContent = "Connecting to repository...";
                                modalCommits.classList.add('hidden');
                                formUpdate.classList.add('hidden');
                                btnClose.textContent = "Cancel";

                                fetch('{{ route('settings.system_update') }}?check=true', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'up_to_date') {
                                        modalTitle.textContent = "System is Up to Date";
                                        modalContent.textContent = data.message;
                                        btnClose.textContent = "Close";
                                    } else if (data.status === 'update_available') {
                                        modalTitle.textContent = "Update Available";
                                        modalContent.textContent = "New versions are available used the following commits:";
                                        modalCommits.textContent = data.commits;
                                        modalCommits.classList.remove('hidden');
                                        formUpdate.classList.remove('hidden');
                                        btnClose.textContent = "Cancel";
                                    } else {
                                        throw new Error(data.message || 'Unknown error');
                                    }
                                })
                                .catch(error => {
                                    modalTitle.textContent = "Error";
                                    modalContent.textContent = "Failed to check for updates: " + error.message;
                                    btnClose.textContent = "Close";
                                });
                            });

                            btnClose.addEventListener('click', function() {
                                modal.classList.add('hidden');
                            });
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>