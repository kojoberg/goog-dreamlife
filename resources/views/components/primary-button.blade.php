@props(['type' => 'submit'])

<button {{ $attributes->merge(['type' => $type, 'class' => 'inline-flex items-center justify-center rounded-lg border border-transparent bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none']) }}>
    {{ $slot }}
</button>