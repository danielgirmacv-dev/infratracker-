<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Task;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display the full activity log.
     */
    public function index(Request $request)
    {
        $activeActor = session('active_actor', 'Infra Director');

        $activeRole = session('active_role', $activeActor);

        $query = Notification::where(function ($q) use ($activeActor, $activeRole) {
            $q->where('target_actor', 'all')
              ->orWhere('target_actor', $activeActor);
            if ($activeRole === 'Employee') {
                $q->orWhere('target_actor', 'Employee');
            }
        })->orderByDesc('created_at');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        // Filter by actor who performed action
        if ($request->filled('actor')) {
            $query->where('actor', $request->string('actor'));
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->string('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->string('date_to'));
        }

        // Filter by search
        if ($request->filled('search')) {
            $term = '%' . $request->string('search') . '%';
            $query->where('message', 'like', $term);
        }

        $activities = $query->paginate(20)->withQueryString();

        $totalTasks = Task::count();
        $allActors  = Notification::distinct()->pluck('actor')->sort()->values();
        $types      = ['created', 'updated', 'deleted'];

        return view('activity.index', compact(
            'activities', 'totalTasks', 'activeActor', 'allActors', 'types'
        ));
    }
}
