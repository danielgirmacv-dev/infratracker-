@extends('layouts.app')
@section('title', 'Search')
@section('header')
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Dashboard</a>
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
        <span class="text-slate-500 dark:text-slate-400">Search</span>
    </div>
@endsection

@section('content')
<div class="space-y-6 animate-slide-up">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="page-title">Search results</h1>
            @if(mb_strlen($query) >= 2)
                <p class="page-subtitle">{{ $totalResults }} {{ Str::plural('result', $totalResults) }} for “{{ $query }}”</p>
            @else
                <p class="page-subtitle">Enter at least 2 characters to search tasks, projects, suppliers, and activity.</p>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('search.index') }}" class="card p-5">
        <label for="search-page-q" class="form-label">Search</label>
        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                <input
                    id="search-page-q"
                    type="search"
                    name="q"
                    value="{{ $query }}"
                    placeholder="Tasks, projects, suppliers, activity…"
                    class="form-input pl-10"
                    autofocus
                    autocomplete="off"
                >
            </div>
            <button type="submit" class="btn-primary shrink-0">Search</button>
        </div>
    </form>

    @if(mb_strlen($query) < 2)
        <div class="card flex flex-col items-center justify-center py-16 text-center">
            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-500/10 ring-1 ring-brand-500/20">
                <svg class="h-7 w-7 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
            </div>
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Start typing to search</p>
            <p class="mt-1 max-w-sm text-xs text-slate-500 dark:text-slate-400">Use the search bar at the top or the field above.</p>
        </div>
    @elseif($totalResults === 0)
        <div class="card flex flex-col items-center justify-center py-16 text-center">
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">No results found</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Try different keywords or check spelling.</p>
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-2">
            @if($tasks->isNotEmpty())
                <div class="card overflow-hidden lg:col-span-2">
                    <div class="panel-header">
                        <h2 class="panel-title">Tasks <span class="font-normal text-slate-400">({{ $tasks->count() }})</span></h2>
                        <a href="{{ route('tasks.index', ['search' => $query]) }}" class="text-xs font-semibold text-brand-600 dark:text-brand-400 hover:underline">View in Tasks →</a>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                        @foreach($tasks as $task)
                            <a href="{{ route('tasks.edit', $task) }}" class="flex items-center gap-4 px-6 py-3.5 transition-colors hover:bg-slate-50 dark:hover:bg-white/[0.02]">
                                <span class="font-mono text-xs text-slate-400">#{{ $task->item_no }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $task->project_name }}</p>
                                    @if($task->task_description)
                                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $task->task_description }}</p>
                                    @endif
                                </div>
                                <x-badge type="status" :value="$task->status" />
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($projects->isNotEmpty())
                <div class="card overflow-hidden">
                    <div class="panel-header">
                        <h2 class="panel-title">Projects <span class="font-normal text-slate-400">({{ $projects->count() }})</span></h2>
                        <a href="{{ route('projects.index', ['search' => $query]) }}" class="text-xs font-semibold text-brand-600 dark:text-brand-400 hover:underline">Manage →</a>
                    </div>
                    <ul class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                        @foreach($projects as $project)
                            <li class="px-6 py-3 text-sm font-medium text-slate-700 dark:text-slate-300">{{ $project->name }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($suppliers->isNotEmpty())
                <div class="card overflow-hidden">
                    <div class="panel-header">
                        <h2 class="panel-title">Suppliers <span class="font-normal text-slate-400">({{ $suppliers->count() }})</span></h2>
                        <a href="{{ route('suppliers.index', ['search' => $query]) }}" class="text-xs font-semibold text-brand-600 dark:text-brand-400 hover:underline">Manage →</a>
                    </div>
                    <ul class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                        @foreach($suppliers as $supplier)
                            <li class="px-6 py-3 text-sm font-medium text-slate-700 dark:text-slate-300">{{ $supplier->name }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($activities->isNotEmpty())
                <div class="card overflow-hidden lg:col-span-2">
                    <div class="panel-header">
                        <h2 class="panel-title">Activity <span class="font-normal text-slate-400">({{ $activities->count() }})</span></h2>
                        <a href="{{ route('activity.index', ['search' => $query]) }}" class="text-xs font-semibold text-brand-600 dark:text-brand-400 hover:underline">Activity log →</a>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                        @foreach($activities as $activity)
                            <div class="px-6 py-3.5">
                                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $activity->message }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-2 text-[10px] text-slate-400">
                                    <span>{{ $activity->actor }}</span>
                                    <span>·</span>
                                    <span>{{ $activity->created_at->diffForHumans() }}</span>
                                    @if($activity->task)
                                        <a href="{{ route('tasks.edit', $activity->task) }}" class="font-semibold text-brand-600 dark:text-brand-400 hover:underline">Open task</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
