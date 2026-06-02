@extends('layouts.app')
@section('title', 'Change Password')
@section('header')
    <div class="breadcrumb">
        <a href="{{ url('/') }}">Dashboard</a>
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
        <span class="text-slate-500 dark:text-slate-400">Settings</span>
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
        <span class="text-slate-500 dark:text-slate-400">Change Password</span>
    </div>
@endsection

@section('content')
<div class="max-w-xl mx-auto space-y-6 animate-slide-up">
    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 rounded-lg border border-emerald-500/20 bg-emerald-100 dark:bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-600 dark:text-emerald-300" x-data x-init="setTimeout(() => $el.remove(), 4000)">
        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="card p-6 border border-slate-200 dark:border-white/5 bg-white dark:bg-white/[0.02]">
        <div class="flex items-center gap-3 mb-6">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-500/20 text-indigo-650 dark:text-indigo-400">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a3 3 0 0 1-3 3m-3-3a3 3 0 0 1 3-3m-3-3H6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 6 21h12a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-200">Change Password</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Update your account password for security.</p>
            </div>
        </div>

        <form action="{{ route('settings.password.update') }}" method="POST" class="space-y-4">
            @csrf
            
            {{-- Current Password --}}
            <div>
                <label for="current_password" class="form-label">Current Password <span class="text-red-600">*</span></label>
                <input id="current_password" type="password" name="current_password" required
                    placeholder="Enter current password..."
                    class="form-input @error('current_password') error @enderror">
                @error('current_password')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- New Password --}}
            <div>
                <label for="new_password" class="form-label">New Password <span class="text-red-600">*</span></label>
                <input id="new_password" type="password" name="new_password" required
                    placeholder="Enter new password..."
                    class="form-input @error('new_password') error @enderror">
                @error('new_password')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="new_password_confirmation" class="form-label">Confirm New Password <span class="text-red-600">*</span></label>
                <input id="new_password_confirmation" type="password" name="new_password_confirmation" required
                    placeholder="Confirm new password..."
                    class="form-input">
            </div>

            <button type="submit" class="btn-primary w-full justify-center mt-6">
                Save New Password
            </button>
        </form>
    </div>
</div>
@endsection
