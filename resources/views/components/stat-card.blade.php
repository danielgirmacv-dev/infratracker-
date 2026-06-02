@props([
    'label'  => '',
    'value'  => '',
    'color'  => '#6366f1',
    'icon'   => '',
    'trend'  => null,
    'accent' => '#6366f1',
])

<div {{ $attributes->class(['stat-card p-5']) }} style="--accent: {{ e($accent) }}">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-bold tracking-tight" style="color: {{ e($color) }}">{{ $value }}</p>
            @if ($trend !== null)
                <p class="mt-1 text-xs text-slate-400 dark:text-slate-600">{{ $trend }}</p>
            @endif
        </div>
        @if ($icon)
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" style="background: {{ e($accent) }}1a">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
