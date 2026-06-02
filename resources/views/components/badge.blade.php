@props([
    'type'  => 'status',
    'value' => '',
])

@php
    $kind = $type === 'priority' ? 'priority' : 'status';

    [$dotColor, $classes] = match ($kind) {
        'status' => match ($value) {
            'Pending'     => ['bg-amber-400',   'bg-amber-400/10 text-amber-300 ring-1 ring-inset ring-amber-400/25'],
            'In Progress' => ['bg-blue-400',    'bg-blue-400/10 text-blue-300 ring-1 ring-inset ring-blue-400/25'],
            'Completed'   => ['bg-emerald-400', 'bg-emerald-400/10 text-emerald-600 dark:text-emerald-300 ring-1 ring-inset ring-emerald-400/25'],
            'On Hold'     => ['bg-slate-400',   'bg-slate-400/10 text-slate-700 dark:text-slate-300 ring-1 ring-inset ring-slate-400/20'],
            default       => ['bg-slate-400',   'bg-slate-400/10 text-slate-700 dark:text-slate-300 ring-1 ring-inset ring-slate-400/20'],
        },
        'priority' => match ($value) {
            'Low'      => ['bg-slate-400',  'bg-slate-400/10 text-slate-700 dark:text-slate-300 ring-1 ring-inset ring-slate-400/20'],
            'Medium'   => ['bg-sky-400',    'bg-sky-400/10 text-sky-300 ring-1 ring-inset ring-sky-400/25'],
            'High'     => ['bg-orange-400', 'bg-orange-400/10 text-orange-600 dark:text-orange-300 ring-1 ring-inset ring-orange-400/25'],
            'Critical' => ['bg-red-400',    'bg-red-400/10 text-red-600 dark:text-red-300 ring-1 ring-inset ring-red-400/25'],
            default    => ['bg-slate-400',  'bg-slate-400/10 text-slate-700 dark:text-slate-300 ring-1 ring-inset ring-slate-400/20'],
        },
        default => ['bg-slate-400', 'bg-slate-400/10 text-slate-700 dark:text-slate-300 ring-1 ring-inset ring-slate-400/20'],
    };
@endphp

<span {{ $attributes->class(['badge', $classes]) }}>
    <span class="badge-dot {{ $dotColor }}"></span>
    {{ $value }}
</span>
