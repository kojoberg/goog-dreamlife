<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Compose Message') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <form action="{{ route('admin.hr.communication.store') }}" method="POST">
                    @csrf

                    <!-- Recipient Type Toggle -->
                    <div class="mb-4" x-data="{ type: 'individual' }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Send To</label>
                        <div class="flex space-x-4 mb-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="recipient_type" value="individual" x-model="type"
                                    class="form-radio text-indigo-600">
                                <span class="ml-2">Individual User</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="recipient_type" value="role" x-model="type"
                                    class="form-radio text-indigo-600">
                                <span class="ml-2">Entire Role Group</span>
                            </label>
                        </div>

                        <!-- Individual Select -->
                        <div x-show="type === 'individual'" class="mb-4">
                            <select name="recipient_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Staff Member...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Role Select -->
                        <div x-show="type === 'role'" class="mb-4" style="display: none;">
                            <select name="recipient_role"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Role...</option>
                                <option value="all">ALL STAFF</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}">{{ ucfirst($role) }}s</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="subject" :value="__('Subject')" />
                        <x-text-input id="subject" class="block mt-1 w-full" type="text" name="subject" required />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="body" :value="__('Message Body')" />
                        <textarea id="body" name="body" rows="6"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required></textarea>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            {{ __('Send Message') }}
                        </x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>