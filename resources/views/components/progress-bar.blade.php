@props([
    'value' => 0,
])

@php
    $value = max(0, min(100, (int) $value));
    $color = match(true) {
        $value >= 100 => 'from-emerald-500 to-emerald-400',
        $value >= 70  => 'from-indigo-500 to-violet-500',
        $value >= 40  => 'from-sky-500 to-indigo-500',
        default       => 'from-slate-500 to-slate-400',
    };
@endphp

<div {{ $attributes->class(['w-full']) }}>
    <div class="flex items-center gap-2">
        <div class="progress-track flex-1">
            <div
                class="progress-fill bg-gradient-to-r {{ $color }}"
                style="width: {{ $value }}%"
                role="progressbar"
                aria-valuenow="{{ $value }}"
                aria-valuemin="0"
                aria-valuemax="100"
            ></div>
        </div>
        <span class="w-8 shrink-0 text-right text-[10px] font-semibold tabular-nums text-slate-400 dark:text-slate-500">{{ $value }}%</span>
    </div>
</div>
