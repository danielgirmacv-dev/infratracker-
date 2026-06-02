@extends('layouts.app')
@section('title', 'Project Names')
@section('header')
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Dashboard</a>
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
        <span class="text-slate-500 dark:text-slate-400">Add Project Name</span>
    </div>
@endsection

@section('content')
<div class="space-y-6 animate-slide-up" x-data="{ deleteModalOpen: false, deleteUrl: '', deleteLabel: '' }">
    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 rounded-lg border border-emerald-500/20 bg-emerald-100 dark:bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-600 dark:text-emerald-300" x-data x-init="setTimeout(() => $el.remove(), 4000)">
        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="flex items-center gap-3 rounded-lg border border-red-500/20 bg-red-100 dark:bg-red-500/10 px-4 py-3 text-sm font-medium text-red-600 dark:text-red-300">
        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
        {{ $errors->first() }}
    </div>
    @endif

    @if(request()->filled('search'))
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-brand-500/20 bg-brand-500/5 px-4 py-3 text-sm">
            <span class="text-slate-600 dark:text-slate-400">Filtered by: <strong class="text-slate-800 dark:text-slate-200">{{ request('search') }}</strong></span>
            <a href="{{ route('projects.index') }}" class="text-xs font-semibold text-brand-600 dark:text-brand-400 hover:underline">Clear filter</a>
        </div>
    @endif

    <div class="flex flex-col gap-6 lg:flex-row">
        {{-- Left Form Panel --}}
        <div class="w-full lg:w-1/3 space-y-6">
            {{-- Manual Add --}}
            <div class="card p-6 border border-slate-200 dark:border-white/5 bg-white dark:bg-white/[0.02]">
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-4">Register New Project</h2>
                
                <form action="{{ route('projects.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="form-label">Project Name <span class="text-red-600">*</span></label>
                        <input id="name" type="text" name="name" required maxlength="255"
                            placeholder="Enter project name..."
                            class="form-input @error('name') error @enderror">
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Add Project Name
                    </button>
                </form>
            </div>

            {{-- Excel Import --}}
            <div class="card p-6 border border-slate-200 dark:border-white/5 bg-white dark:bg-white/[0.02]">
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-1">Import from Excel</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Upload an Excel or CSV file with a column header: <code class="bg-slate-100 dark:bg-white/5 px-1.5 py-0.5 rounded text-[11px] font-mono">project_name</code> or <code class="bg-slate-100 dark:bg-white/5 px-1.5 py-0.5 rounded text-[11px] font-mono">name</code></p>
                
                <form action="{{ route('projects.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="file" class="form-label">Excel / CSV File <span class="text-red-600">*</span></label>
                        <input id="file" type="file" name="file" required accept=".xlsx,.xls,.csv"
                            class="block w-full text-sm text-slate-500 dark:text-slate-400
                                file:mr-3 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-xs file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                dark:file:bg-indigo-500/10 dark:file:text-indigo-300
                                hover:file:bg-indigo-100 dark:hover:file:bg-indigo-500/20
                                file:transition file:cursor-pointer">
                    </div>

                    <button type="submit" class="btn-secondary w-full justify-center">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" /></svg>
                        Import Projects
                    </button>
                </form>
            </div>
        </div>

        {{-- Right List Panel --}}
        <div class="w-full lg:w-2/3">
            <div class="card overflow-hidden border border-slate-200 dark:border-white/5 bg-white dark:bg-white/[0.02]">
                <div class="border-b border-slate-200 dark:border-white/5 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-base font-bold text-slate-800 dark:text-slate-200">Registered Project Names</h2>
                    <span class="rounded-full bg-indigo-100 dark:bg-indigo-500/20 px-2 py-0.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400">{{ $projects->count() }} Total</span>
                </div>

                <div class="table-scroll-wrap">
                    <table class="data-table w-full min-w-[28rem]">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Project Name</th>
                                <th>Created At</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($projects as $idx => $project)
                                <tr>
                                    <td class="font-mono text-xs text-slate-400 dark:text-slate-500">{{ $idx + 1 }}</td>
                                    <td class="font-medium text-slate-800 dark:text-slate-200">{{ $project->name }}</td>
                                    <td class="text-slate-500 dark:text-slate-400 text-xs">{{ $project->created_at->format('d M Y, h:i A') }}</td>
                                    <td class="text-right">
                                        <button
                                            type="button"
                                            class="inline-flex rounded-lg p-1.5 text-red-600 dark:text-red-400 transition hover:bg-red-100 dark:hover:bg-red-500/10"
                                            title="Delete"
                                            @click="deleteModalOpen=true; deleteUrl=@js(route('projects.destroy',$project)); deleteLabel=@js($project->name)"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-slate-500 dark:text-slate-400">
                                        No project names registered yet. Add one manually or import from Excel.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div
        x-show="deleteModalOpen"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
        role="dialog" aria-modal="true"
    >
        <div
            class="card w-full max-w-md overflow-hidden shadow-2xl"
            @click.outside="deleteModalOpen = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
        >
            <div class="border-b border-red-500/20 bg-red-50 dark:bg-red-500/5 px-6 py-4">
                <h2 class="flex items-center gap-2 text-base font-semibold text-red-600 dark:text-red-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                    Delete Project Name
                </h2>
            </div>
            <div class="px-6 py-5">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Are you sure you want to delete
                    <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="deleteLabel"></span>?
                </p>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-200 dark:border-white/5 px-6 py-4">
                <button type="button" class="btn-ghost" @click="deleteModalOpen = false">Cancel</button>
                <form method="post" :action="deleteUrl" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
