@props(['active'])

@php
    $classes = ($active ?? false)
        ? 'inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium leading-5 text-white bg-indigo-600 rounded-full focus:outline-none transition duration-150 ease-in-out shadow-sm'
        : 'inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-900 hover:bg-gray-100/50 rounded-full focus:outline-none focus:text-gray-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>