@extends('layouts.app')

@section('title', 'New Task')

@section('header')
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Dashboard</a>
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
        <a href="{{ route('tasks.index') }}">Tasks</a>
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
        <span class="text-slate-500 dark:text-slate-400">New Task</span>
    </div>
@endsection

@section('content')
<div class="animate-slide-up">

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="page-title">New Task</h1>
            <p class="page-subtitle">Fill in the details below to create a new infrastructure task.</p>
        </div>
        <a href="{{ route('tasks.index') }}" class="btn-ghost hidden sm:inline-flex">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" /></svg>
            Back
        </a>
    </div>

    {{-- Form Card --}}
    <form
        method="post"
        action="{{ route('tasks.store') }}"
        class="mx-auto max-w-4xl"
        id="create-task-form"
    >
        @csrf

        <div class="card p-6 sm:p-8">
            @include('tasks._form', ['task' => null])
        </div>

        {{-- Sticky Save Bar --}}
        <div class="sticky-save-bar sticky bottom-0 z-10 mt-4 rounded-lg border border-slate-200 px-6 py-4 backdrop-blur-xl flex flex-wrap items-center justify-between gap-3 dark:border-white/5">
            <p class="text-xs text-slate-400 dark:text-slate-500">
                <span class="text-indigo-600 dark:text-indigo-400">*</span> Required fields must be filled before saving.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('tasks.index') }}" class="btn-ghost">Cancel</a>
                <button type="submit" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Create Task
                </button>
            </div>
        </div>

    </form>
</div>
@endsection
