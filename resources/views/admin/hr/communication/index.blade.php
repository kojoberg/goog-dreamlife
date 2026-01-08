<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Internal Inbox') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Messages</h3>
                    <a href="{{ route('admin.hr.communication.create') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                        Compose New Message
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">From</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">To</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($messages as $message)
                                <tr class="cursor-pointer hover:bg-gray-50"
                                    onclick="window.location='{{ route('admin.hr.communication.show', $message) }}'">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $message->sender->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ Str::limit($message->subject, 40) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($message->recipient_rule)
                                            <span
                                                class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ ucfirst($message->recipient_role) }}s</span>
                                        @else
                                            Me
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                        {{ $message->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        @if($message->is_read)
                                            <span class="text-green-600">Read</span>
                                        @else
                                            <span class="text-indigo-600 font-bold">Unread</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4 px-4">
                        {{ $messages->links() }}
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>