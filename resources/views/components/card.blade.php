<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-100']) }}>
    <div class="p-6 text-slate-800">
        {{ $slot }}
    </div>
</div>