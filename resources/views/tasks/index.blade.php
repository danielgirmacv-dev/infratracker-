@extends('layouts.app')
@section('title', 'Tasks')
@section('header')
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Dashboard</a>
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
        <span class="text-slate-500 dark:text-slate-400">Tasks</span>
    </div>
@endsection

@section('content')
<div
    class="space-y-6 animate-slide-up"
    x-data="{ viewMode: 'table', deleteModalOpen: false, deleteUrl: '', deleteLabel: '' }"
    @keydown.escape.window="deleteModalOpen = false"
>
    {{-- Flash message --}}
    @if(session('success'))
    <div class="flex items-center gap-3 rounded-lg border border-emerald-500/20 bg-emerald-100 dark:bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-600 dark:text-emerald-300" x-data x-init="setTimeout(() => $el.remove(), 4000)">
        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="page-title">Tasks</h1>
            <p class="page-subtitle">{{ $tasks->count() }} {{ Str::plural('task', $tasks->count()) }} {{ request()->anyFilled(['search','status','priority','department','date_from','date_to']) ? 'matching your filters' : 'total' }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- View Mode Toggle --}}
            <div class="inline-flex rounded-xl bg-slate-100 dark:bg-white/[0.04] p-1 ring-1 ring-slate-200 dark:ring-white/5 mr-2">
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all"
                    :class="viewMode === 'table' ? 'bg-white dark:bg-white/10 text-indigo-600 dark:text-indigo-300 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'"
                    @click="viewMode = 'table'"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6Z" /></svg>
                    Table
                </button>
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all"
                    :class="viewMode === 'kanban' ? 'bg-white dark:bg-white/10 text-indigo-600 dark:text-indigo-300 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300'"
                    @click="viewMode = 'kanban'"
                >
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m6-15v15m-12-3h18m-18-6h18m-18-6h18" /></svg>
                    Kanban
                </button>
            </div>

            @if($activeActor !== 'Employee')
                <a href="{{ route('tasks.create') }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Add Task
                </a>
            @endif
            <a href="{{ route('tasks.export', request()->query()) }}" class="btn-secondary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                Export Excel
            </a>
        </div>
    </div>

    {{-- Filter Panel --}}
    <form method="get" action="{{ route('tasks.index') }}" class="card-glass p-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-end">
            {{-- Search --}}
            <div class="min-w-[13rem] flex-1">
                <label for="filter-search" class="form-label">Search</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0Z" /></svg>
                    <input id="filter-search" type="search" name="search" value="{{ request('search') }}" placeholder="Project, description, assignee…" class="form-input pl-9">
                </div>
            </div>
            {{-- Status --}}
            <div class="w-full sm:w-40">
                <label for="filter-status" class="form-label">Status</label>
                <select id="filter-status" name="status" class="form-input" @change="$el.closest('form').submit()">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Priority --}}
            <div class="w-full sm:w-40">
                <label for="filter-priority" class="form-label">Priority</label>
                <select id="filter-priority" name="priority" class="form-input" @change="$el.closest('form').submit()">
                    <option value="">All priorities</option>
                    @foreach ($priorities as $p)
                        <option value="{{ $p }}" @selected(request('priority') === $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Department --}}
            <div class="w-full sm:w-48">
                <label for="filter-department" class="form-label">Department</label>
                <select id="filter-department" name="department" class="form-input" @change="$el.closest('form').submit()">
                    <option value="">All departments</option>
                    @foreach ($departments as $d)
                        <option value="{{ $d }}" @selected(request('department') === $d)>{{ $d }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Date From --}}
            <div class="w-full sm:w-40">
                <label for="filter-date-from" class="form-label">From date</label>
                <input id="filter-date-from" type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
            </div>
            {{-- Date To --}}
            <div class="w-full sm:w-40">
                <label for="filter-date-to" class="form-label">To date</label>
                <input id="filter-date-to" type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
            </div>
            {{-- Actions --}}
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">Apply</button>
                @if(request()->anyFilled(['search','status','priority','department','date_from','date_to']))
                    <a href="{{ route('tasks.index') }}" class="btn-ghost">Reset</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table View --}}
    <div x-show="viewMode === 'table'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="card overflow-hidden">
        <div class="overflow-x-auto scrollbar-none">
            <table class="data-table min-w-[1100px]">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Project</th>
                        <th class="min-w-[8rem]">Description</th>
                        <th>Supplier</th>
                        <th>Amount</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th class="min-w-[7rem]">Progress</th>
                        <th class="min-w-[6rem]">Next Action</th>
                        <th>Department</th>
                        <th>Given By</th>
                        <th>Given To</th>
                        <th class="min-w-[5rem]">Remark</th>
                        <th class="sticky-action sticky right-0 z-10 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            <td class="font-mono text-xs text-slate-400 dark:text-slate-500">{{ $task->item_no }}</td>
                            <td class="whitespace-nowrap text-slate-500 dark:text-slate-400">{{ $task->date?->format('d M Y') }}</td>
                            <td class="max-w-[10rem] truncate font-medium text-slate-800 dark:text-slate-200" title="{{ $task->project_name }}">{{ $task->project_name }}</td>
                            <td class="max-w-[14rem] truncate text-slate-500 dark:text-slate-400" title="{{ $task->task_description }}">{{ Str::limit($task->task_description ?? '', 40) }}</td>
                            <td class="max-w-[8rem] truncate text-slate-500 dark:text-slate-400" title="{{ $task->supplier_name }}">{{ $task->supplier_name ?? '—' }}</td>
                            <td class="whitespace-nowrap tabular-nums text-slate-700 dark:text-slate-300">
                                @if($task->amount !== null && $task->amount !== '')
                                    <span class="text-xs text-slate-400 dark:text-slate-500">ETB</span> {{ number_format((float)$task->amount, 2) }}
                                @else —
                                @endif
                            </td>
                            <td class="whitespace-nowrap text-slate-500 dark:text-slate-400">{{ $task->start_date?->format('d M Y') }}</td>
                            <td class="whitespace-nowrap {{ $task->end_date && $task->end_date->isPast() && $task->status !== 'Completed' ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-slate-500 dark:text-slate-400' }}">
                                {{ $task->end_date?->format('d M Y') }}
                            </td>
                            <td class="whitespace-nowrap"><x-badge type="status" :value="$task->status" /></td>
                            <td class="whitespace-nowrap"><x-badge type="priority" :value="$task->priority" /></td>
                            <td class="align-middle">
                                <div class="w-28"><x-progress-bar :value="$task->progress" /></div>
                            </td>
                            <td class="max-w-[8rem] truncate text-slate-500 dark:text-slate-400" title="{{ $task->next_action }}">{{ $task->next_action ?? '—' }}</td>
                            <td class="max-w-[8rem] truncate text-slate-700 dark:text-slate-300" title="{{ $task->responsible_department }}">{{ $task->responsible_department }}</td>
                            <td class="max-w-[8rem] truncate text-slate-700 dark:text-slate-300" title="{{ $task->task_given_by }}">{{ $task->task_given_by ?? 'Infra Director' }}</td>
                            <td class="max-w-[8rem] truncate text-slate-700 dark:text-slate-300" title="{{ $task->task_given_to }}">{{ $task->task_given_to }}</td>
                            <td class="max-w-[8rem] truncate text-slate-500 dark:text-slate-400" title="{{ $task->remark }}">{{ $task->remark ?? '—' }}</td>
                             <td class="sticky-action sticky right-0 z-10 whitespace-nowrap text-right">
                                @include('tasks._row-actions', ['task' => $task])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="h-10 w-10 text-slate-400 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0Z" /></svg>
                                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No tasks match your filters</p>
                                    <a href="{{ route('tasks.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">Clear filters</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Kanban View --}}
    <div x-show="viewMode === 'kanban'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" x-cloak>
        @php
            $statuses = ['Pending', 'In Progress', 'On Hold', 'Completed'];
            $groupedTasks = $tasks->groupBy('status');
        @endphp
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4 items-start">
            @foreach($statuses as $status)
                @php
                    $statusTasks = $groupedTasks->get($status, collect());
                    $headerColors = [
                        'Pending' => ['border' => 'border-t-amber-500', 'bg' => 'bg-amber-500/10', 'text' => 'text-amber-600 dark:text-amber-400', 'dot' => 'bg-amber-500'],
                        'In Progress' => ['border' => 'border-t-blue-500', 'bg' => 'bg-blue-500/10', 'text' => 'text-blue-600 dark:text-blue-400', 'dot' => 'bg-blue-500'],
                        'On Hold' => ['border' => 'border-t-slate-500', 'bg' => 'bg-slate-500/10', 'text' => 'text-slate-500 dark:text-slate-400', 'dot' => 'bg-slate-500'],
                        'Completed' => ['border' => 'border-t-emerald-500', 'bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-600 dark:text-emerald-400', 'dot' => 'bg-emerald-500'],
                    ];
                    $theme = $headerColors[$status];
                @endphp
                
                <div class="flex flex-col rounded-2xl border border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.01] p-4 {{ $theme['border'] }} border-t-4 shadow-sm min-h-[500px]">
                    {{-- Column Header --}}
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200 dark:border-white/5">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full {{ $theme['dot'] }}"></span>
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $status }}</span>
                        </div>
                        <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $theme['bg'] }} {{ $theme['text'] }}">{{ $statusTasks->count() }}</span>
                    </div>

                    {{-- Cards List --}}
                    <div class="space-y-4 max-h-[600px] overflow-y-auto pr-1 scrollbar-none">
                        @forelse($statusTasks as $task)
                            <div class="card p-4 transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 border border-slate-200 dark:border-white/5 hover:border-slate-300 dark:hover:border-white/10 group relative bg-white dark:bg-white/[0.02]">
                                {{-- Card Content --}}
                                <div class="space-y-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="text-[10px] font-mono text-slate-400 dark:text-slate-500">#{{ $task->item_no }}</span>
                                        <x-badge type="priority" :value="$task->priority" />
                                    </div>

                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-1" title="{{ $task->project_name }}">{{ $task->project_name }}</h3>
                                        @if($task->task_description)
                                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 line-clamp-2" title="{{ $task->task_description }}">{{ $task->task_description }}</p>
                                        @endif
                                    </div>

                                    {{-- Assignees --}}
                                    <div class="flex flex-col gap-1 border-t border-slate-100 dark:border-white/[0.04] pt-2 text-[10px] text-slate-400 dark:text-slate-500">
                                        <div class="flex justify-between">
                                            <span>By: <strong class="text-slate-600 dark:text-slate-400">{{ $task->task_given_by ?? 'Infra Director' }}</strong></span>
                                            <span>To: <strong class="text-slate-600 dark:text-slate-400">{{ $task->task_given_to }}</strong></span>
                                        </div>
                                        @if($task->responsible_department)
                                            <div class="text-right">
                                                <span class="bg-slate-100 dark:bg-white/5 rounded px-1 text-[9px] text-slate-500 dark:text-slate-400">{{ $task->responsible_department }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Progress --}}
                                    <div class="space-y-1">
                                        <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500">
                                            <span>Progress</span>
                                            <span class="font-semibold tabular-nums text-slate-600 dark:text-slate-400">{{ $task->progress }}%</span>
                                        </div>
                                        <x-progress-bar :value="$task->progress" />
                                    </div>

                                    {{-- End Date & Overdue Info --}}
                                    @if($task->end_date)
                                        @php
                                            $isOverdue = $task->end_date->isPast() && $task->status !== 'Completed';
                                        @endphp
                                        <div class="flex items-center gap-1 text-[10px] {{ $isOverdue ? 'text-red-500 dark:text-red-400 font-bold' : 'text-slate-400 dark:text-slate-500' }}">
                                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                                            <span>Due: {{ $task->end_date?->format('d M Y') }}</span>
                                            @if($isOverdue)
                                                <span class="bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-300 rounded px-1 py-0.5 text-[8px] font-semibold tracking-wide uppercase ml-auto">Overdue</span>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Action buttons --}}
                                    <div class="flex items-center justify-end border-t border-slate-100 dark:border-white/[0.04] pt-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @include('tasks._row-actions', ['task' => $task, 'iconClass' => 'h-3.5 w-3.5', 'btnPadding' => 'p-1'])
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-10 text-center border border-dashed border-slate-200 dark:border-white/5 rounded-xl bg-slate-50/20 dark:bg-white/[0.005]">
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">No tasks in this status</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Delete Modal --}}
    <div
        x-show="deleteModalOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
        role="dialog" aria-modal="true" aria-labelledby="delete-modal-title"
        >
            <div
            class="card w-full max-w-md overflow-hidden shadow-2xl"
            @click.outside="deleteModalOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="border-b border-red-500/20 bg-red-50 dark:bg-red-500/5 px-6 py-4">
                <h2 id="delete-modal-title" class="flex items-center gap-2 text-base font-semibold text-red-600 dark:text-red-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                    Delete Task
                </h2>
            </div>
            <div class="px-6 py-5">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    This will permanently delete
                    <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="deleteLabel"></span>.
                    This action cannot be undone.
                </p>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-200 dark:border-white/5 px-6 py-4">
                <button type="button" class="btn-ghost" @click="deleteModalOpen = false">Cancel</button>
                <form method="post" :action="deleteUrl" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Delete permanently</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
