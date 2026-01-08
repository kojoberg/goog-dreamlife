<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Read Message') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">

                <div class="border-b pb-4 mb-4">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $message->subject }}</h1>
                    <div class="mt-2 flex justify-between text-sm text-gray-600">
                        <div>
                            From: <strong class="text-gray-900">{{ $message->sender->name }}</strong>
                            <span class="mx-2">|</span>
                            {{ $message->created_at->format('M d, Y h:i A') }}
                        </div>
                        <div>
                            To:
                            {{ $message->recipient_id ? $message->recipient->name : ucfirst($message->recipient_role) . 's' }}
                        </div>
                    </div>
                </div>

                <div class="prose max-w-none text-gray-800">
                    {!! nl2br(e($message->body)) !!}
                </div>

                <div class="mt-8 pt-4 border-t flex justify-end">
                    <a href="{{ route('admin.hr.communication.index') }}"
                        class="text-indigo-600 hover:text-indigo-900">Back to Inbox</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>