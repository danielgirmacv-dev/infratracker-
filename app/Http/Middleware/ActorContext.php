<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use App\Models\Manager;
use App\Models\Notification;
use App\Support\ActorSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class ActorContext
{
    public function handle(Request $request, Closure $next)
    {
        $isLoginRoute = $request->is('login');

        if (!$request->session()->has('active_actor')) {
            if (!$isLoginRoute) {
                return redirect()->route('login');
            }

            return $next($request);
        }

        if ($isLoginRoute) {
            return redirect()->route('dashboard');
        }

        $activeActor = $request->session()->get('active_actor');

        if (!$request->session()->has('active_role')) {
            $request->session()->put('active_role', ActorSession::loginRoleForActor($activeActor));
        }

        $activeRole = $request->session()->get('active_role');

        $notificationsQuery = Notification::query()
            ->where(function ($query) use ($activeActor, $activeRole) {
                $query->where('target_actor', 'all')
                    ->orWhere('target_actor', $activeActor);

                if ($activeRole === 'Employee') {
                    $query->orWhere('target_actor', 'Employee');
                }
            })
            ->orderByDesc('created_at')
            ->limit(10);

        $unreadCount = Notification::query()
            ->where(function ($query) use ($activeActor, $activeRole) {
                $query->where('target_actor', 'all')
                    ->orWhere('target_actor', $activeActor);

                if ($activeRole === 'Employee') {
                    $query->orWhere('target_actor', 'Employee');
                }
            })
            ->where(function ($query) use ($activeRole) {
                if ($activeRole === 'Infra Director') {
                    $query->where('read_by_director', false);
                } elseif ($activeRole === 'Project Manager') {
                    $query->where('read_by_manager', false);
                } else {
                    $query->where('read_by_employee', false);
                }
            })
            ->count();

        View::share('activeActor', $activeActor);
        View::share('activeRole', $activeRole);
        View::share('employees', Schema::hasTable('employees')
            ? Employee::orderBy('name')->get()
            : collect());
        View::share('managers', Schema::hasTable('managers')
            ? Manager::orderBy('name')->get()
            : collect());
        View::share('actorNotifications', $notificationsQuery->get());
        View::share('unreadNotificationsCount', $unreadCount);

        return $next($request);
    }
}
