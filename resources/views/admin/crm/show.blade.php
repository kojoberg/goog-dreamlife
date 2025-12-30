<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Campaign Report') }}: {{ $campaign->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
                    <div class="p-4 bg-gray-50 rounded">
                        <span class="block text-2xl font-bold">{{ $campaign->stats['total'] }}</span>
                        <span class="text-gray-500">Total Recipients</span>
                    </div>
                    <div class="p-4 bg-green-50 rounded">
                        <span class="block text-2xl font-bold text-green-600">{{ $campaign->stats['sent'] }}</span>
                        <span class="text-green-600">Sent</span>
                    </div>
                    <div class="p-4 bg-red-50 rounded">
                        <span class="block text-2xl font-bold text-red-600">{{ $campaign->stats['failed'] }}</span>
                        <span class="text-red-600">Failed</span>
                    </div>
                    <div class="p-4 bg-yellow-50 rounded">
                        <span class="block text-2xl font-bold text-yellow-600">{{ $campaign->stats['pending'] }}</span>
                        <span class="text-yellow-600">Pending</span>
                    </div>
                </div>
            </x-card>

            <x-card>
                <h3 class="font-bold text-lg mb-4">Recipient Details</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Recipient</th>
                                <th class="px-6 py-3 text-left">Contact</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Sent At</th>
                                <th class="px-6 py-3 text-left">Error</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($campaign->recipients as $recipient)
                                <tr>
                                    <td class="px-6 py-4">{{ class_basename($recipient->recipient_type) }}
                                        #{{ $recipient->recipient_id }}</td>
                                    <td class="px-6 py-4">{{ $recipient->contact }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($recipient->status === 'sent') bg-green-100 text-green-800
                                                @elseif($recipient->status === 'failed') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($recipient->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        {{ $recipient->sent_at ? $recipient->sent_at->format('H:i:s') : '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-red-500">{{ Str::limit($recipient->error, 50) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            <div class="mt-4">
                <a href="{{ route('admin.crm.index') }}" class="text-blue-600 hover:underline">&larr; Back to CRM
                    Dashboard</a>
            </div>
        </div>
    </div>
</x-app-layout>