@extends('layouts.app')
@section('title', 'Activity Log')
@section('header')
    <div class="breadcrumb">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        Activity Log
    </div>
@endsection

@section('content')
<div class="space-y-6 animate-slide-up">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="page-title">Activity Log</h1>
            <p class="page-subtitle">Complete history of all task actions and changes.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn-secondary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" /></svg>
            Back to Dashboard
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('activity.index') }}" class="card p-5">
        <div class="flex flex-wrap items-end gap-4">
            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="form-label">Search</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    <input id="search" name="search" type="text" value="{{ request('search') }}" placeholder="Search activity…"
                        class="form-input pl-10">
                </div>
            </div>

            {{-- Type Filter --}}
            <div class="w-36">
                <label for="type" class="form-label">Type</label>
                <select id="type" name="type" class="form-input">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Actor Filter --}}
            <div class="w-44">
                <label for="actor" class="form-label">Performed By</label>
                <select id="actor" name="actor" class="form-input">
                    <option value="">All Actors</option>
                    @foreach($allActors as $actor)
                        <option value="{{ $actor }}" @selected(request('actor') === $actor)>{{ $actor }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date From --}}
            <div class="w-40">
                <label for="date_from" class="form-label">From</label>
                <input id="date_from" name="date_from" type="date" value="{{ request('date_from') }}" class="form-input">
            </div>

            {{-- Date To --}}
            <div class="w-40">
                <label for="date_to" class="form-label">To</label>
                <input id="date_to" name="date_to" type="date" value="{{ request('date_to') }}" class="form-input">
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" /></svg>
                    Filter
                </button>
                @if(request()->hasAny(['search', 'type', 'actor', 'date_from', 'date_to']))
                    <a href="{{ route('activity.index') }}" class="btn-secondary">Clear</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Results Summary --}}
    <div class="flex items-center justify-between">
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
            Showing {{ $activities->firstItem() ?? 0 }}–{{ $activities->lastItem() ?? 0 }} of {{ $activities->total() }} activities
        </p>
    </div>

    {{-- Activity Timeline --}}
    <div class="card overflow-hidden">
        <div class="divide-y divide-slate-100 dark:divide-white/[0.04]">
            @forelse($activities as $activity)
                @php
                    $typeColor = match($activity->type) {
                        'created' => ['dot' => 'bg-emerald-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-500/10', 'text' => 'text-emerald-700 dark:text-emerald-400', 'ring' => 'ring-emerald-500/20'],
                        'updated' => ['dot' => 'bg-blue-500', 'bg' => 'bg-blue-50 dark:bg-blue-500/10', 'text' => 'text-blue-700 dark:text-blue-400', 'ring' => 'ring-blue-500/20'],
                        'deleted' => ['dot' => 'bg-red-500', 'bg' => 'bg-red-50 dark:bg-red-500/10', 'text' => 'text-red-700 dark:text-red-400', 'ring' => 'ring-red-500/20'],
                        default   => ['dot' => 'bg-slate-400', 'bg' => 'bg-slate-50 dark:bg-white/5', 'text' => 'text-slate-600 dark:text-slate-400', 'ring' => 'ring-slate-400/20'],
                    };
                @endphp
                <div class="flex items-start gap-4 px-6 py-4 transition-colors hover:bg-slate-50 dark:hover:bg-white/[0.02]">
                    {{-- Type Dot --}}
                    <div class="mt-1.5 flex h-3 w-3 shrink-0 items-center justify-center">
                        <span class="flex h-2.5 w-2.5 rounded-full {{ $typeColor['dot'] }}"></span>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200 leading-relaxed">{{ $activity->message }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            {{-- Type Badge --}}
                            <span class="inline-flex items-center gap-1 rounded-full {{ $typeColor['bg'] }} {{ $typeColor['text'] }} px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide ring-1 ring-inset {{ $typeColor['ring'] }}">
                                {{ ucfirst($activity->type) }}
                            </span>
                            {{-- Actor Badge --}}
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 dark:bg-white/5 px-2.5 py-0.5 text-[10px] font-semibold text-slate-600 dark:text-slate-400 ring-1 ring-inset ring-slate-200 dark:ring-white/10">
                                <span class="flex h-1.5 w-1.5 rounded-full {{ $activity->actor === 'Infra Director' ? 'bg-indigo-500' : ($activity->actor === 'Project Manager' ? 'bg-emerald-500' : 'bg-amber-500') }}"></span>
                                {{ $activity->actor }}
                            </span>
                            {{-- Timestamp --}}
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                {{ $activity->created_at->format('M d, Y') }} at {{ $activity->created_at->format('h:i A') }}
                                <span class="text-slate-300 dark:text-slate-600 mx-0.5">·</span>
                                {{ $activity->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    {{-- View Task Link --}}
                    @if($activity->task)
                        <a href="{{ route('tasks.edit', $activity->task) }}" class="shrink-0 flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-50 hover:border-indigo-200 dark:border-white/10 dark:bg-white/[0.04] dark:text-indigo-400 dark:hover:bg-indigo-500/10">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                            View Task
                        </a>
                    @endif
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 dark:bg-white/5 mb-4">
                        <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">No activities found</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">
                        @if(request()->hasAny(['search', 'type', 'actor', 'date_from', 'date_to']))
                            Try adjusting your filters to see more results.
                        @else
                            Activities will appear here as tasks are created and updated.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($activities->hasPages())
        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-white/[0.02]">
            <div class="flex items-center gap-2">
                @if($activities->onFirstPage())
                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-400 cursor-not-allowed dark:bg-white/5 dark:text-slate-600">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                        Previous
                    </span>
                @else
                    <a href="{{ $activities->previousPageUrl() }}" class="inline-flex items-center gap-1 rounded-lg bg-white border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:bg-white/[0.04] dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/[0.07]">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                        Previous
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-1">
                @foreach($activities->getUrlRange(max(1, $activities->currentPage() - 2), min($activities->lastPage(), $activities->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold transition {{ $page == $activities->currentPage() ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/5' }}">
                        {{ $page }}
                    </a>
                @endforeach
            </div>

            <div class="flex items-center gap-2">
                @if($activities->hasMorePages())
                    <a href="{{ $activities->nextPageUrl() }}" class="inline-flex items-center gap-1 rounded-lg bg-white border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 dark:bg-white/[0.04] dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/[0.07]">
                        Next
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-400 cursor-not-allowed dark:bg-white/5 dark:text-slate-600">
                        Next
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                    </span>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection
