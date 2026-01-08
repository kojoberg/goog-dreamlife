@props(['title', 'actions' => null])

<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">
            {{ $title }}
        </h1>
        @if(isset($subtitle))
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>

    @if(isset($actions) || $actions)
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>