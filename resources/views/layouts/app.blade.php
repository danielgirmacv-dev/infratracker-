<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'InfraTracker')) — InfraTracker</title>
    <meta name="description" content="InfraTracker — Infrastructure project task management system">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        (() => {
            const storedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = storedTheme || (prefersDark ? 'dark' : 'light');

            document.documentElement.classList.toggle('dark', theme === 'dark');
            document.documentElement.style.colorScheme = theme;
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
</head>
<body
    class="min-h-screen antialiased"
    x-data="{ 
        sidebarOpen: window.innerWidth >= 1024,
        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
            document.documentElement.style.colorScheme = this.theme;
        }
    }"
    @keydown.escape.window="sidebarOpen = false"
>
    {{-- ─── Mobile Overlay ─────────────────────────────────────── --}}
    <div
        x-show="sidebarOpen"
        x-cloak
        class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm lg:hidden"
        @click="sidebarOpen = false"
        x-transition.opacity
    ></div>

    {{-- ─── Sidebar ─────────────────────────────────────────────── --}}
    <aside
        class="sidebar fixed left-0 top-0 z-40 flex h-screen flex-col transition-transform duration-300 lg:translate-x-0"
        style="width: var(--sidebar-width)"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        {{-- Logo --}}
        <div class="flex h-16 items-center gap-3 border-b border-slate-200 dark:border-white/5 px-5">
            <div class="flex h-8 w-8 items-center justify-center">
                <svg width="24" height="28" viewBox="0 0 40 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="14" y="0" width="12" height="48" fill="#1cb4c9"/>
                    <rect x="20" y="0" width="20" height="8" rx="2.5" fill="#1cb4c9"/>
                    <rect x="0" y="8" width="20" height="8" rx="2.5" fill="#1cb4c9"/>
                    <rect x="20" y="16" width="20" height="8" rx="2.5" fill="#1cb4c9"/>
                    <rect x="0" y="24" width="20" height="8" rx="2.5" fill="#1cb4c9"/>
                    <rect x="20" y="32" width="20" height="8" rx="2.5" fill="#1cb4c9"/>
                    <rect x="0" y="40" width="20" height="8" rx="2.5" fill="#1cb4c9"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold leading-none sidebar-logo"><span class="text-[#1cb4c9]">EEC</span> InfraTracker</p>
                <p class="mt-0.5 text-[10px] text-slate-400 dark:text-slate-500 leading-none">Task Management</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex flex-col gap-0.5 p-3 flex-1 overflow-y-auto scrollbar-none">
            <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-600">Menu</p>

            @php
                $dashboardActive = request()->is('/');
                $tasksActive     = request()->routeIs('tasks.*');
                $projectsActive  = request()->routeIs('projects.*');
                $suppliersActive = request()->routeIs('suppliers.*');
                $settingsActive  = request()->routeIs('settings.*');
                $activityActive  = request()->routeIs('activity.*');
            @endphp

            {{-- Dashboard --}}
            <a href="{{ url('/') }}" class="nav-item {{ $dashboardActive ? 'active' : '' }}">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                </svg>
                Dashboard
            </a>

            {{-- Tasks --}}
            <a href="{{ route('tasks.index') }}" class="nav-item {{ $tasksActive ? 'active' : '' }}">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                </svg>
                Tasks
                @if(isset($totalTasks) && $totalTasks > 0)
                    <span class="ml-auto rounded-full bg-indigo-100 dark:bg-indigo-500/20 px-2 py-0.5 text-[10px] font-semibold text-indigo-600 dark:text-indigo-400">
                        {{ $totalTasks }}
                    </span>
                @endif
            </a>

            {{-- Projects --}}
            <a href="{{ route('projects.index') }}" class="nav-item {{ $projectsActive ? 'active' : '' }}">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18a2.25 2.25 0 0 1 2.25 2.25v4.5A2.25 2.25 0 0 1 18 21.75H6a2.25 2.25 0 0 1-2.25-2.25v-4.5a2.25 2.25 0 0 1 2.25-2.25ZM6 2.25h12A2.25 2.25 0 0 1 20.25 4.5V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18V4.5A2.25 2.25 0 0 1 6 2.25Z" />
                </svg>
                Add Project Name
            </a>

            {{-- Suppliers --}}
            <a href="{{ route('suppliers.index') }}" class="nav-item {{ $suppliersActive ? 'active' : '' }}">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125a1.125 1.125 0 0 0 1.125-1.125V9.75M8.25 13.875c0-1.125.67-2.16 1.714-2.616l5.625-2.41a3.375 3.375 0 0 1 4.662 3.102m-12 1.924h12.75" />
                </svg>
                Add Supplier Name
            </a>

            {{-- Change Password --}}
            <a href="{{ route('settings.password') }}" class="nav-item {{ $settingsActive ? 'active' : '' }}">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a3 3 0 0 1-3 3m-3-3a3 3 0 0 1 3-3m-3-3H6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 6 21h12a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
                Change Password
            </a>

            {{-- Activity Log --}}
            <a href="{{ route('activity.index') }}" class="nav-item {{ $activityActive ? 'active' : '' }}">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75m-7.5 6h13.5a2.25 2.25 0 0 0 2.25-2.25V6.108a2.25 2.25 0 0 0-1.976-2.192 48.424 48.424 0 0 0-1.123-.08M9.75 3.75H12a2.25 2.25 0 0 1 2.25 2.25v.75M9 3.75h-.75A2.25 2.25 0 0 0 6 6v.75m3 0V6A2.25 2.25 0 0 1 9.75 3.75m0 0H12" />
                </svg>
                Activity Log
            </a>
        </nav>

        {{-- Footer --}}
        <div class="border-t border-slate-200 dark:border-white/5 px-4 py-3">
            <p class="text-[10px] text-slate-400 dark:text-slate-600 leading-relaxed">
                © {{ date('Y') }} InfraTracker<br>
                Infrastructure Tracker v1.0
            </p>
        </div>
    </aside>

    {{-- ─── Main Wrapper ────────────────────────────────────────── --}}
    <div class="flex min-h-screen flex-col transition-all duration-300 lg:pl-[var(--sidebar-width)]">

        {{-- Top Header --}}
        <header class="topbar sticky top-0 z-20 flex items-center gap-4 px-4 lg:px-8">
            {{-- Hamburger --}}
            <button
                @click="sidebarOpen = !sidebarOpen"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-100 lg:hidden"
                aria-label="Toggle sidebar"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            {{-- Page title slot (set via @section('header')) --}}
            <div class="flex-1 min-w-0">
                @hasSection('header')
                    @yield('header')
                @else
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">@yield('title', 'InfraTracker')</span>
                @endif
            </div>

            {{-- Right actions --}}
            <div class="flex items-center gap-3">
                {{-- Notification Center --}}
                <div class="relative" x-data="{ notificationsOpen: false }" @click.outside="notificationsOpen = false">
                    <button
                        type="button"
                        @click="notificationsOpen = !notificationsOpen"
                        class="relative flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-900 dark:border-white/10 dark:bg-white/[0.04] dark:text-slate-400 dark:hover:bg-white/[0.07] dark:hover:text-slate-100"
                        aria-haspopup="menu"
                        :aria-expanded="notificationsOpen.toString()"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a9.04 9.04 0 0 1-1.657 0 9.04 9.04 0 0 1-1.657 0m0 0a9.041 9.041 0 0 1-3.62-4.72m3.62 4.72a9.041 9.041 0 0 0 3.62-4.72M12 2.25c-3.15 0-5.719 2.457-5.719 5.485 0 2.898-.95 5.421-2.42 7.643h16.278c-1.47-2.222-2.42-4.745-2.42-7.643 0-3.028-2.569-5.485-5.719-5.485Z" />
                        </svg>
                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <span class="absolute top-1.5 right-1.5 flex h-2 w-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-[#070e1e]"></span>
                        @endif
                    </button>

                    <div
                        x-show="notificationsOpen"
                        x-cloak
                        x-transition.origin.top.right
                        class="absolute right-0 mt-2 w-80 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-xl shadow-slate-900/10 dark:border-white/10 dark:bg-[#070e1e] dark:shadow-black/30"
                    >
                        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-white/10">
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100 font-sans">Notifications</p>
                            @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                <form action="{{ route('notifications.read-all') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        Mark all read
                                    </button>
                                </form>
                            @endif
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-white/5">
                            @if(isset($actorNotifications) && $actorNotifications->isNotEmpty())
                                @foreach($actorNotifications as $notification)
                                    @php
                                        $isUnread = false;
                                        if (isset($activeActor)) {
                                            if ($activeActor === 'Infra Director') {
                                                $isUnread = !$notification->read_by_director;
                                            } elseif ($activeActor === 'Project Manager') {
                                                $isUnread = !$notification->read_by_manager;
                                            } else {
                                                $isUnread = !$notification->read_by_employee;
                                            }
                                        }
                                    @endphp
                                    <div class="px-4 py-3 text-xs transition-colors hover:bg-slate-50 dark:hover:bg-white/[0.02] {{ $isUnread ? 'bg-indigo-50/50 dark:bg-indigo-500/5' : '' }}">
                                        <div class="flex items-start gap-2.5">
                                            <span class="mt-0.5 flex h-2 w-2 shrink-0 rounded-full {{ $notification->type === 'created' ? 'bg-emerald-500' : 'bg-blue-500' }}"></span>
                                            <div class="flex-1">
                                                <p class="text-slate-700 dark:text-slate-300 leading-relaxed">{{ $notification->message }}</p>
                                                <p class="mt-1 text-[10px] text-slate-400 dark:text-slate-500">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-6 text-center text-slate-400 dark:text-slate-500">
                                    <svg class="mx-auto h-8 w-8 opacity-35 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a9.04 9.04 0 0 1-1.657 0 9.04 9.04 0 0 1-1.657 0m0 0a9.041 9.041 0 0 1-3.62-4.72m3.62 4.72a9.041 9.041 0 0 0 3.62-4.72M12 2.25c-3.15 0-5.719 2.457-5.719 5.485 0 2.898-.95 5.421-2.42 7.643h16.278c-1.47-2.222-2.42-4.745-2.42-7.643 0-3.028-2.569-5.485-5.719-5.485Z" />
                                    </svg>
                                    <p class="text-xs">No notifications yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Theme Toggle --}}
                <button
                    @click="toggleTheme()"
                    class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-slate-100"
                    aria-label="Toggle dark mode"
                    :title="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
                >
                    <svg x-show="theme === 'dark'" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    <svg x-show="theme === 'light'" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </button>

                {{-- Active Actor Badge --}}
                @if(isset($activeActor))
                    <div class="inline-flex h-9 items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 text-sm font-semibold text-slate-700 dark:border-white/10 dark:bg-white/[0.04] dark:text-slate-200">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full text-[9px] font-bold {{ $activeActor === 'Infra Director' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300' : ($activeActor === 'Project Manager' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300') }}">
                            {{ substr($activeActor, 0, 1) }}
                        </span>
                        <span class="hidden sm:inline font-sans text-xs">{{ $activeActor }}</span>
                    </div>
                @endif

                {{-- Logout Button --}}
                <form action="{{ route('logout') }}" method="POST" class="inline-block">
                    @csrf
                    <button
                        type="submit"
                        class="flex h-9 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-bold text-red-600 transition hover:bg-red-50 dark:border-white/10 dark:bg-white/[0.04] dark:text-red-400 dark:hover:bg-red-950/20"
                        title="Logout"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                        <span class="hidden md:inline">Logout</span>
                    </button>
                </form>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 p-4 lg:p-8 animate-fade-in">
            @yield('content')
        </main>
    </div>

    <style>[x-cloak]{display:none!important}</style>
</body>
</html>
