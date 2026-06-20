@extends('layouts.app')
@section('title', 'Employees')
@section('header')
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Dashboard</a>
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
        <span class="text-slate-500 dark:text-slate-400">Employees</span>
    </div>
@endsection

@section('content')
<div class="space-y-6 animate-slide-up" x-data="{ deleteModalOpen: false, deleteUrl: '', deleteLabel: '' }">
    @if(session('success'))
    <div class="flex items-center gap-3 rounded-lg border border-emerald-500/20 bg-emerald-100 dark:bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-600 dark:text-emerald-300" x-data x-init="setTimeout(() => $el.remove(), 5000)">
        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 rounded-lg border border-red-500/20 bg-red-100 dark:bg-red-500/10 px-4 py-3 text-sm font-medium text-red-600 dark:text-red-300">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="flex items-center gap-3 rounded-lg border border-red-500/20 bg-red-100 dark:bg-red-500/10 px-4 py-3 text-sm font-medium text-red-600 dark:text-red-300">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="page-title">Employees</h1>
            <p class="page-subtitle">Add team members with a login password. They must change it after their first sign-in.</p>
        </div>
    </div>

    <div class="flex flex-col gap-6 lg:flex-row">
        <div class="w-full lg:w-1/3">
            <div class="card p-6">
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-4">Add Employee</h2>
                <form action="{{ route('employees.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="form-label">Employee name <span class="text-red-600">*</span></label>
                        <input id="name" type="text" name="name" required maxlength="100"
                            value="{{ old('name') }}"
                            placeholder="e.g. FEVEN"
                            class="form-input uppercase @error('name') error @enderror">
                        <p class="mt-1 text-[10px] text-slate-500 dark:text-slate-400">Stored in uppercase. Used at login and for task assignment.</p>
                    </div>
                    <div>
                        <label for="password" class="form-label">Initial password <span class="text-red-600">*</span></label>
                        <input id="password" type="password" name="password" required minlength="4"
                            class="form-input @error('password') error @enderror">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="form-label">Confirm password <span class="text-red-600">*</span></label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required minlength="4"
                            class="form-input">
                    </div>
                    <div>
                        <label for="location_id" class="form-label">Location</label>
                        <select id="location_id" name="location_id" class="form-input">
                            <option value="">None / Select Location</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="department_id" class="form-label">Department</label>
                        <select id="department_id" name="department_id" class="form-input">
                            <option value="">None / Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" /></svg>
                        Add Employee
                    </button>
                </form>
            </div>
        </div>

        <div class="w-full lg:w-2/3">
            <div class="card overflow-hidden">
                <div class="panel-header">
                    <h2 class="panel-title">Registered employees</h2>
                    <span class="badge bg-brand-500/10 text-brand-700 dark:text-brand-400 ring-1 ring-inset ring-brand-500/25">{{ $employees->count() }} total</span>
                </div>
                <div class="table-scroll-wrap">
                    <table class="data-table w-full min-w-[32rem]">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Department</th>
                                <th>Login</th>
                                <th>Created</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($employees as $idx => $employee)
                                <tr>
                                    <td class="font-mono text-xs text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="font-bold text-slate-800 dark:text-slate-200">{{ $employee->name }}</td>
                                    <td class="text-xs text-slate-500 dark:text-slate-400">
                                        @if(isset($usersByName[$employee->name]) && $usersByName[$employee->name]->location)
                                            {{ $usersByName[$employee->name]->location->name }}
                                        @else
                                            <span class="text-slate-400 dark:text-slate-600">—</span>
                                        @endif
                                    </td>
                                    <td class="text-xs text-slate-500 dark:text-slate-400">
                                        @if(isset($usersByName[$employee->name]) && $usersByName[$employee->name]->department)
                                            {{ $usersByName[$employee->name]->department->name }}
                                        @else
                                            <span class="text-slate-400 dark:text-slate-600">—</span>
                                        @endif
                                    </td>
                                    <td class="text-xs text-slate-500 dark:text-slate-400">
                                        @if(isset($usersByName[$employee->name]))
                                            @if($usersByName[$employee->name]->must_change_password)
                                                <span class="text-amber-600 dark:text-amber-400">Awaiting password change</span>
                                            @else
                                                <span class="text-emerald-600 dark:text-emerald-400">Active</span>
                                            @endif
                                        @else
                                            <span class="text-red-600 dark:text-red-400">No login</span>
                                        @endif
                                    </td>
                                    <td class="text-xs text-slate-500 dark:text-slate-400">{{ $employee->created_at->format('d M Y') }}</td>
                                    <td class="text-right">
                                        @if(($canDelete ?? false) && ($employee->name !== 'FEVEN' || $employees->count() > 1))
                                            <button
                                                type="button"
                                                class="inline-flex rounded-lg p-1.5 text-red-600 dark:text-red-400 transition hover:bg-red-100 dark:hover:bg-red-500/10"
                                                title="Delete"
                                                @click="deleteModalOpen=true; deleteUrl=@js(route('employees.destroy', $employee)); deleteLabel=@js($employee->name)"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                            </button>
                                        @elseif(!($canDelete ?? false))
                                            <span class="text-[10px] text-slate-400">—</span>
                                        @else
                                            <span class="text-[10px] text-slate-400">Primary</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-500">No employees yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div x-show="deleteModalOpen" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="card w-full max-w-md overflow-hidden shadow-2xl" @click.outside="deleteModalOpen = false">
            <div class="border-b border-red-500/20 bg-red-50 dark:bg-red-500/5 px-6 py-4">
                <h2 class="text-base font-semibold text-red-600 dark:text-red-300">Delete employee</h2>
            </div>
            <div class="px-6 py-5 text-sm text-slate-600 dark:text-slate-400">
                Remove <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="deleteLabel"></span>? They will no longer be able to log in.
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
