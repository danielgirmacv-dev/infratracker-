<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->input('q', ''));
        $totalTasks = Task::count();

        if (mb_strlen($query) < 2) {
            return view('search.index', [
                'query' => $query,
                'tasks' => collect(),
                'projects' => collect(),
                'suppliers' => collect(),
                'activities' => collect(),
                'totalResults' => 0,
                'totalTasks' => $totalTasks,
            ]);
        }

        $results = $this->search($query, $request->session()->get('active_actor', 'Infra Director'));

        return view('search.index', array_merge($results, [
            'query' => $query,
            'totalResults' => $results['tasks']->count()
                + $results['projects']->count()
                + $results['suppliers']->count()
                + $results['activities']->count(),
            'totalTasks' => $totalTasks,
        ]));
    }

    public function quick(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'query' => $query,
                'tasks' => [],
                'projects' => [],
                'suppliers' => [],
                'activities' => [],
                'total' => 0,
            ]);
        }

        $results = $this->search(
            $query,
            $request->session()->get('active_actor', 'Infra Director'),
            limits: ['tasks' => 5, 'projects' => 4, 'suppliers' => 4, 'activities' => 4]
        );

        return response()->json([
            'query' => $query,
            'tasks' => $results['tasks']->map(fn (Task $t) => [
                'label' => $t->project_name,
                'meta' => $t->status.' · #'.$t->item_no,
                'url' => route('tasks.edit', $t),
            ])->values(),
            'projects' => $results['projects']->map(fn (Project $p) => [
                'label' => $p->name,
                'meta' => 'Project',
                'url' => route('projects.index', ['search' => $query]),
            ])->values(),
            'suppliers' => $results['suppliers']->map(fn (Supplier $s) => [
                'label' => $s->name,
                'meta' => 'Supplier',
                'url' => route('suppliers.index', ['search' => $query]),
            ])->values(),
            'activities' => $results['activities']->map(fn (Notification $n) => [
                'label' => \Illuminate\Support\Str::limit($n->message, 60),
                'meta' => $n->actor.' · '.$n->created_at->diffForHumans(),
                'url' => $n->task ? route('tasks.edit', $n->task) : route('activity.index', ['search' => $query]),
            ])->values(),
            'total' => $results['tasks']->count()
                + $results['projects']->count()
                + $results['suppliers']->count()
                + $results['activities']->count(),
            'results_url' => route('search.index', ['q' => $query]),
        ]);
    }

    /**
     * @param  array<string, int>|null  $limits
     * @return array{tasks: \Illuminate\Support\Collection, projects: \Illuminate\Support\Collection, suppliers: \Illuminate\Support\Collection, activities: \Illuminate\Support\Collection}
     */
    private function search(string $query, string $activeActor, ?array $limits = null): array
    {
        $term = '%'.$query.'%';

        $taskLimit = $limits['tasks'] ?? 50;
        $projectLimit = $limits['projects'] ?? 20;
        $supplierLimit = $limits['suppliers'] ?? 20;
        $activityLimit = $limits['activities'] ?? 20;

        $tasks = Task::query()
            ->where(function (Builder $q) use ($term): void {
                $q->where('project_name', 'like', $term)
                    ->orWhere('task_description', 'like', $term)
                    ->orWhere('supplier_name', 'like', $term)
                    ->orWhere('task_given_to', 'like', $term)
                    ->orWhere('task_given_by', 'like', $term)
                    ->orWhere('responsible_department', 'like', $term)
                    ->orWhere('next_action', 'like', $term)
                    ->orWhere('remark', 'like', $term)
                    ->orWhere('status', 'like', $term);
            })
            ->orderByDesc('item_no')
            ->limit($taskLimit)
            ->get();

        $projects = Project::query()
            ->where('name', 'like', $term)
            ->orderBy('name')
            ->limit($projectLimit)
            ->get();

        $suppliers = Supplier::query()
            ->where('name', 'like', $term)
            ->orderBy('name')
            ->limit($supplierLimit)
            ->get();

        $activities = Notification::query()
            ->where(function (Builder $q) use ($activeActor): void {
                $q->where('target_actor', 'all')
                    ->orWhere('target_actor', $activeActor);
            })
            ->where('message', 'like', $term)
            ->with('task')
            ->orderByDesc('created_at')
            ->limit($activityLimit)
            ->get();

        return compact('tasks', 'projects', 'suppliers', 'activities');
    }
}
