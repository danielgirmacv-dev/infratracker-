@extends('layouts.app')
@section('title', 'Dashboard')
@section('header')
    <div class="breadcrumb">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6Z" /></svg>
        Dashboard
    </div>
@endsection

@section('content')
<div class="space-y-8 animate-slide-up">

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Overview of all infrastructure tasks and progress.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(($activeRole ?? '') !== 'Employee')
                <a href="{{ route('tasks.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Add Task
                </a>
            @endif
            <a href="{{ route('tasks.export', []) }}" class="btn-secondary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                Export
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="stat-card p-5" style="--accent:#6366f1">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Tasks</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">{{ $total }}</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-600">All active projects</p>
                </div>
                <div class="stat-card-icon">
                    <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" /></svg>
                </div>
            </div>
        </div>

        <div class="stat-card p-5" style="--accent:#3b82f6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">In Progress</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-blue-600 dark:text-blue-400">{{ $inProgress }}</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-600">{{ $total > 0 ? round(($inProgress / max($total,1)) * 100) : 0 }}% of total</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-500/10">
                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                </div>
            </div>
        </div>

        <div class="stat-card p-5" style="--accent:#10b981">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Completed</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-emerald-600 dark:text-emerald-400">{{ $completed }}</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-600">{{ $total > 0 ? round(($completed / max($total,1)) * 100) : 0 }}% completion rate</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-500/10">
                    <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
            </div>
        </div>

        <div class="stat-card p-5" style="--accent:{{ $overdue > 0 ? '#ef4444' : '#475569' }}">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Overdue</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight {{ $overdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-500 dark:text-slate-400' }}">{{ $overdue }}</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-600">{{ $overdue > 0 ? 'Needs attention' : 'All on track' }}</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $overdue > 0 ? 'bg-red-100 dark:bg-red-500/10' : 'bg-slate-500/10' }}">
                    <svg class="h-5 w-5 {{ $overdue > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-400 dark:text-slate-500' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Breakdown --}}
    @if($total > 0)
    <div class="card p-6">
        <h2 class="mb-5 text-sm font-semibold text-slate-700 dark:text-slate-300">Status Breakdown</h2>
        <div class="space-y-3">
            @php
                $statusItems = [
                    ['label'=>'Completed',   'count'=>$completed,  'color'=>'bg-emerald-500','text'=>'text-emerald-600 dark:text-emerald-400'],
                    ['label'=>'In Progress', 'count'=>$inProgress, 'color'=>'bg-blue-500',   'text'=>'text-blue-600 dark:text-blue-400'],
                    ['label'=>'Pending',     'count'=>$pending,    'color'=>'bg-amber-500',  'text'=>'text-amber-600 dark:text-amber-400'],
                    ['label'=>'On Hold',     'count'=>$onHold,     'color'=>'bg-slate-500',  'text'=>'text-slate-500 dark:text-slate-400'],
                ];
            @endphp
            @foreach($statusItems as $item)
                @php $pct = $total > 0 ? round(($item['count'] / $total) * 100) : 0; @endphp
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                    <span class="w-full shrink-0 text-xs font-medium text-slate-500 dark:text-slate-400 sm:w-24">{{ $item['label'] }}</span>
                    <div class="progress-track flex-1">
                        <div class="{{ $item['color'] }} h-full rounded-full transition-all duration-700" style="width:{{ $pct }}%"></div>
                    </div>
                    <span class="{{ $item['text'] }} w-16 text-right text-xs font-semibold tabular-nums">
                        {{ $item['count'] }} <span class="text-slate-400 dark:text-slate-600">({{ $pct }}%)</span>
                    </span>
                </div>
            @endforeach
        </div>
        <div class="mt-5 flex flex-wrap items-center gap-3 border-t border-slate-200 dark:border-white/5 pt-5">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-600">Priority</span>
            <span class="badge bg-red-400/10 text-red-600 dark:text-red-300 ring-1 ring-inset ring-red-400/25"><span class="badge-dot bg-red-400"></span>Critical — {{ $critical }}</span>
            <span class="badge bg-orange-400/10 text-orange-600 dark:text-orange-300 ring-1 ring-inset ring-orange-400/25"><span class="badge-dot bg-orange-400"></span>High — {{ $high }}</span>
        </div>
    </div>
    @endif

    {{-- Recent Tasks & Activity Feed --}}
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            @if($recentTasks->isNotEmpty())
                <div class="card overflow-hidden">
                    <div class="panel-header">
                        <h2 class="panel-title">Recent Tasks</h2>
                        <a href="{{ route('tasks.index') }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-600 dark:text-indigo-300 transition-colors">View all →</a>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-white/[0.04]">
                        @foreach($recentTasks as $task)
                        <div class="flex flex-col gap-3 px-4 py-3.5 transition-colors hover:bg-indigo-50 dark:hover:bg-indigo-500/5 sm:flex-row sm:items-center sm:gap-4 sm:px-6">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-slate-800 dark:text-slate-200">{{ $task->project_name }}</p>
                                <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500 line-clamp-2 sm:truncate">
                                    <span class="text-slate-500 dark:text-slate-400">By:</span> {{ $task->task_given_by ?? 'Infra Director' }} · <span class="text-slate-500 dark:text-slate-400">To:</span> {{ $task->task_given_to ?? '—' }}{{ $task->responsible_department ? ' · '.$task->responsible_department : '' }}
                                </p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 sm:shrink-0">
                                <x-badge type="priority" :value="$task->priority" />
                                <x-badge type="status"   :value="$task->status" />
                                <div class="w-full sm:hidden">
                                    <x-progress-bar :value="$task->progress" />
                                </div>
                            </div>
                            <div class="w-20 shrink-0 hidden sm:block">
                                <x-progress-bar :value="$task->progress" />
                            </div>
                            @php
                                $canEditRecent = in_array($activeRole ?? '', ['Infra Director', 'Project Manager'], true)
                                    || (($activeRole ?? '') === 'Employee' && ($task->task_given_to === $activeActor || ($task->task_given_to === 'Employee' && $activeActor === 'FEVEN')));
                            @endphp
                            @if($canEditRecent)
                                <a href="{{ route('tasks.edit', $task) }}" class="shrink-0 rounded-lg p-1.5 text-slate-400 dark:text-slate-500 transition hover:bg-indigo-100 dark:bg-indigo-500/10 hover:text-indigo-600 dark:text-indigo-400" title="Edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                                </a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="card flex flex-col items-center justify-center py-16 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-100 dark:bg-indigo-500/10 mb-4">
                        <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" /></svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">No tasks yet</p>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Get started by creating your first task.</p>
                    @if(($activeRole ?? '') !== 'Employee')
                        <a href="{{ route('tasks.create') }}" class="btn-primary mt-5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Create Task
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <div>
            {{-- Activity Feed --}}
            <div class="card overflow-hidden">
                <div class="panel-header">
                    <h2 class="panel-title">Activity Feed</h2>
                    <span class="badge bg-brand-500/10 text-brand-700 dark:text-brand-400 ring-1 ring-inset ring-brand-500/25">{{ $activityFeed->total() }} total</span>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-white/[0.04] p-4">
                    @forelse($activityFeed as $feed)
                        <div class="py-3 first:pt-0 last:pb-0">
                            <div class="flex items-start gap-3">
                                <span class="mt-1.5 flex h-2 w-2 shrink-0 rounded-full
                                    {{ $feed->type === 'created' ? 'bg-emerald-500' : ($feed->type === 'updated' ? 'bg-blue-500' : 'bg-amber-500') }}">
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-slate-700 dark:text-slate-300 font-medium leading-relaxed">{{ $feed->message }}</p>
                                    <div class="mt-1 flex items-center justify-between gap-2">
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">{{ $feed->created_at->diffForHumans() }}</span>
                                        @if($feed->task)
                                            <a href="{{ route('tasks.edit', $feed->task) }}" class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline">View Task →</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 dark:bg-white/5">
                                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            </div>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">No activity recorded yet.</p>
                        </div>
                    @endforelse
                </div>
                @if($activityFeed->hasPages())
                    <div class="border-t border-slate-200 dark:border-white/5 px-4 py-3 space-y-2">
                        <p class="text-center text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                            Iteration {{ $activityFeed->currentPage() }} of {{ $activityFeed->lastPage() }}
                        </p>
                        <div class="flex items-center justify-between gap-2">
                            @if($activityFeed->onFirstPage())
                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1.5 text-[10px] font-semibold text-slate-400 cursor-not-allowed dark:bg-white/5 dark:text-slate-600">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                                    Prev
                                </span>
                            @else
                                <a href="{{ $activityFeed->previousPageUrl() }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[10px] font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-white/[0.04] dark:text-slate-300 dark:hover:bg-white/[0.07]">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                                    Prev
                                </a>
                            @endif

                            <div class="flex items-center gap-1">
                                @foreach($activityFeed->getUrlRange(max(1, $activityFeed->currentPage() - 2), min($activityFeed->lastPage(), $activityFeed->currentPage() + 2)) as $page => $url)
                                    <a href="{{ $url }}" class="flex h-7 min-w-[1.75rem] items-center justify-center rounded-lg px-1.5 text-[10px] font-bold transition {{ $page == $activityFeed->currentPage() ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-white/5' }}" title="Iteration {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>

                            @if($activityFeed->hasMorePages())
                                <a href="{{ $activityFeed->nextPageUrl() }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-[10px] font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-white/[0.04] dark:text-slate-300 dark:hover:bg-white/[0.07]">
                                    Next
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1.5 text-[10px] font-semibold text-slate-400 cursor-not-allowed dark:bg-white/5 dark:text-slate-600">
                                    Next
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
                @if($activityFeed->total() > 0)
                    <div class="border-t border-slate-200 dark:border-white/5 px-6 py-3">
                        <a href="{{ route('activity.index') }}" class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-50 px-4 py-2.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:hover:bg-indigo-500/20 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                            View All {{ $activityFeed->total() }} Activities
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
