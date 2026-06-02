<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Notification;

class ActorContext
{
    /**
     * Handle an incoming request.
     */
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

        // Fetch notifications relevant to the active actor
        $notificationsQuery = Notification::query()
            ->where(function ($query) use ($activeActor) {
                $query->where('target_actor', 'all')
                      ->orWhere('target_actor', $activeActor);
            })
            ->orderByDesc('created_at')
            ->limit(10);

        // Fetch unread notifications count
        $unreadCount = Notification::query()
            ->where(function ($query) use ($activeActor) {
                $query->where('target_actor', 'all')
                      ->orWhere('target_actor', $activeActor);
            })
            ->where(function ($query) use ($activeActor) {
                if ($activeActor === 'Infra Director') {
                    $query->where('read_by_director', false);
                } elseif ($activeActor === 'Project Manager') {
                    $query->where('read_by_manager', false);
                } else {
                    $query->where('read_by_employee', false);
                }
            })
            ->count();

        $notifications = $notificationsQuery->get();

        // Share globally with all Blade views
        View::share('activeActor', $activeActor);
        View::share('actorNotifications', $notifications);
        View::share('unreadNotificationsCount', $unreadCount);

        return $next($request);
    }
}
