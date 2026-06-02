<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\ActorSession;
use Closure;
use Illuminate\Http\Request;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        if (!ActorSession::isEmployee()) {
            return $next($request);
        }

        if ($request->routeIs('settings.password', 'settings.password.update', 'logout')) {
            return $next($request);
        }

        $user = User::where('name', ActorSession::name())->first();

        if ($user?->must_change_password) {
            return redirect()
                ->route('settings.password')
                ->with('warning', 'Please set a new password before continuing.');
        }

        return $next($request);
    }
}
