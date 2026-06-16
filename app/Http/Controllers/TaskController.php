<?php

namespace App\Http\Controllers;

use App\Exports\TaskExport;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Manager;
use App\Services\CloudflareTurnstile;
use App\Support\ActorSession;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TaskController extends Controller
{
    /**
     * Dashboard — real aggregate stats.
     */
    public function dashboard(Request $request)
    {
        $total      = Task::count();
        $inProgress = Task::where('status', 'In Progress')->count();
        $completed  = Task::where('status', 'Completed')->count();
        $pending    = Task::where('status', 'Pending')->count();
        $onHold     = Task::where('status', 'On Hold')->count();
        $overdue    = Task::whereNotNull('end_date')
                          ->whereDate('end_date', '<', now())
                          ->whereNotIn('status', ['Completed'])
                          ->count();

        $critical   = Task::where('priority', 'Critical')->count();
        $high       = Task::where('priority', 'High')->count();

        $recentTasks = Task::orderByDesc('item_no')->limit(6)->get();

        $totalTasks = $total; // used in sidebar badge

        $activeActor = session('active_actor', 'Infra Director');
        $activeRole = session('active_role', $activeActor);
        $activityFeed = \App\Models\Notification::where(function ($q) use ($activeActor, $activeRole) {
            $q->where('target_actor', 'all')
              ->orWhere('target_actor', $activeActor);
            if ($activeRole === 'Employee') {
                $q->orWhere('target_actor', 'Employee');
            }
        })->orderByDesc('created_at')
          ->paginate(5, ['*'], 'iteration')
          ->withQueryString();

        return view('dashboard', compact(
            'total', 'inProgress', 'completed', 'pending',
            'onHold', 'overdue', 'critical', 'high',
            'recentTasks', 'totalTasks', 'activityFeed', 'activeActor'
        ));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = $this->filteredTasksQuery($request)->orderByDesc('item_no')->get();

        $statuses    = ['Pending', 'In Progress', 'Completed', 'On Hold'];
        $priorities  = ['Low', 'Medium', 'High', 'Critical'];
        $departments = Task::query()
            ->whereNotNull('responsible_department')
            ->where('responsible_department', '!=', '')
            ->distinct()
            ->orderBy('responsible_department')
            ->pluck('responsible_department');

        $totalTasks = Task::count(); // sidebar badge

        return view('tasks.index', compact('tasks', 'statuses', 'priorities', 'departments', 'totalTasks'));
    }

    /**
     * Export tasks (XLSX), honoring the same filters as index.
     */
    public function export(Request $request)
    {
        return Excel::download(
            new TaskExport($this->filteredTasksQuery($request)),
            'tasks.xlsx'
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $activeActor = session('active_actor', 'Infra Director');
        if (ActorSession::isEmployee()) {
            return redirect()->route('tasks.index')->with('error', 'Employees are not authorized to create tasks.');
        }

        $totalTasks = Task::count();
        return view('tasks.create', compact('totalTasks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        if (ActorSession::isEmployee()) {
            return redirect()->route('tasks.index')->with('error', 'Employees are not authorized to create tasks.');
        }

        $data = $request->validated();
        $activeActor = session('active_actor', 'Infra Director');
        $data['task_given_by'] = $activeActor;
        $data['status'] = 'Pending';
        $data['progress'] = 0;
        
        $data['start_date'] = $data['start_date'] ?? now()->toDateString();
        $data['responsible_department'] = $data['responsible_department'] ?? 'Not Assigned';

        $task = Task::create($data);

        // Trigger Notification
        \App\Models\Notification::create([
            'task_id' => $task->item_no,
            'actor' => $activeActor,
            'type' => 'created',
            'message' => "{$activeActor} assigned task '{$task->project_name}' to {$task->task_given_to}.",
            'target_actor' => $task->task_given_to,
            'read_by_director' => $activeActor === 'Infra Director',
            'read_by_manager' => ActorSession::isManager(),
            'read_by_employee' => ActorSession::isTaskAssignee($task->task_given_to),
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $activeActor = session('active_actor', 'Infra Director');
        if (!$this->canModifyTask($task, $activeActor)) {
            return redirect()->route('tasks.index')->with('error', 'You are not authorized to edit this task.');
        }

        $totalTasks = Task::count();
        return view('tasks.edit', compact('task', 'totalTasks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        $activeActor = session('active_actor', 'Infra Director');
        if (!$this->canModifyTask($task, $activeActor)) {
            return redirect()->route('tasks.index')->with('error', 'You are not authorized to edit this task.');
        }

        $oldStatus = $task->status;
        $oldProgress = $task->progress;
        $oldPriority = $task->priority;
        $oldAssignee = $task->task_given_to;

        $task->update($request->validated());

        // Construct update notification message
        $changes = [];
        if ($oldStatus !== $task->status) {
            $changes[] = "status changed to '{$task->status}'";
        }
        if ($oldProgress !== (int)$task->progress) {
            $changes[] = "progress updated to {$task->progress}%";
        }
        if ($oldPriority !== $task->priority) {
            $changes[] = "priority updated to '{$task->priority}'";
        }
        if ($oldAssignee !== $task->task_given_to) {
            $changes[] = "reassigned to {$task->task_given_to}";
        }

        if (!empty($changes)) {
            $changesStr = implode(', ', $changes);
            $message = "{$activeActor} updated task '{$task->project_name}': {$changesStr}.";
        } else {
            $message = "{$activeActor} updated task '{$task->project_name}'.";
        }

        // Trigger Notification for everyone
        \App\Models\Notification::create([
            'task_id' => $task->item_no,
            'actor' => $activeActor,
            'type' => 'updated',
            'message' => $message,
            'target_actor' => 'all',
            'read_by_director' => $activeActor === 'Infra Director',
            'read_by_manager' => ActorSession::isManager(),
            'read_by_employee' => ActorSession::isTaskAssignee($task->task_given_to),
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if (!ActorSession::canManageTasks()) {
            return redirect()->route('tasks.index')->with('error', 'You are not authorized to delete this task.');
        }

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }

    /**
     * Show the login page.
     */
    public function showLogin()
    {
        $employees = Employee::orderBy('name')->get();
        $managers = Manager::orderBy('name')->get();

        $actors = collect([
            ['name' => 'Infra Director', 'label' => 'Infra Director', 'hint' => 'Full oversight & task delegation', 'initials' => 'ID', 'tone' => 'director'],
        ])->merge($managers->map(fn ($m) => [
            'name' => $m->name,
            'label' => $m->name,
            'hint' => 'Coordinate projects & manage employees',
            'initials' => strtoupper(substr($m->name, 0, 2)),
            'tone' => 'manager',
        ]))->merge($employees->map(fn ($e) => [
            'name' => $e->name,
            'label' => $e->name,
            'hint' => 'Execute tasks & report progress',
            'initials' => strtoupper(substr($e->name, 0, 2)),
            'tone' => 'employee',
        ]));

        return view('login', [
            'turnstileSiteKey' => config('services.turnstile.site_key'),
            'actors' => $actors,
        ]);
    }

    /**
     * Authenticate actor login.
     */
    public function login(Request $request, CloudflareTurnstile $turnstile)
    {
        $rules = [
            'username' => 'required|string|max:100',
            'password' => 'required|string',
        ];

        if ($turnstile->isConfigured()) {
            $rules['cf-turnstile-response'] = 'required|string';
        }

        $request->validate($rules);

        if ($turnstile->isConfigured() && !$turnstile->verify(
            $request->input('cf-turnstile-response'),
            $request->ip()
        )) {
            return back()
                ->withErrors(['turnstile' => 'Robot verification failed. Please try again.'])
                ->withInput($request->except('password', 'cf-turnstile-response'));
        }

        $actor = $this->resolveLoginActor($request->input('username'));

        if (!$actor) {
            return back()
                ->withErrors(['username' => 'No account found with that username.'])
                ->withInput($request->except('password'));
        }

        $password = $request->input('password');
        $role = ActorSession::loginRoleForActor($actor);

        if ($actor === 'Infra Director') {
            $user = \App\Models\User::firstOrCreate(
                ['name' => $actor],
                [
                    'email' => 'infradirector@infratracker.local',
                    'password' => 'director123',
                    'must_change_password' => false,
                ]
            );
        } else {
            $user = \App\Models\User::where('name', $actor)->first();

            if (!$user) {
                $hint = Manager::isManagerName($actor) || Employee::isEmployeeName($actor)
                    ? 'Ask the Director or your Manager to add you with a password.'
                    : 'No login account for this user. Ask the Director to add you with a password.';

                return back()->withErrors([
                    'username' => $hint,
                ])->withInput($request->except('password'));
            }
        }

        if (!\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            return back()->withErrors(['password' => 'Invalid credentials. Please try again.'])->withInput($request->except('password'));
        }

        $request->session()->regenerate();
        $request->session()->put('active_actor', $actor);
        $request->session()->put('active_role', $role);

        return redirect()->route('dashboard')->with('success', "Welcome back, {$actor}!");
    }

    private function resolveLoginActor(string $username): ?string
    {
        $input = trim($username);
        if ($input === '') {
            return null;
        }

        $normalized = strtolower($input);

        if (in_array($normalized, ['infra director', 'director', 'id'], true)) {
            return 'Infra Director';
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('managers')) {
            $manager = Manager::query()
                ->whereRaw('UPPER(name) = ?', [Manager::normalizeName($input)])
                ->first();

            if ($manager) {
                return $manager->name;
            }
        }

        if (in_array($normalized, ['project manager', 'project cordinator', 'project coordinator', 'coordinator', 'manager', 'pc'], true)) {
            $firstManager = Manager::query()->orderBy('name')->first();

            return $firstManager?->name;
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('employees')) {
            $employee = Employee::query()
                ->whereRaw('UPPER(name) = ?', [Employee::normalizeName($input)])
                ->first();

            if ($employee) {
                return $employee->name;
            }
        }

        $user = \App\Models\User::query()
            ->whereRaw('UPPER(name) = ?', [strtoupper($input)])
            ->first();

        return $user?->name;
    }

    /**
     * Log out the active actor.
     */
    public function logout(Request $request)
    {
        $request->session()->forget(['active_actor', 'active_role']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    /**
     * Mark all notifications as read for current active actor.
     */
    public function markAllNotificationsRead(Request $request)
    {
        $activeActor = $request->session()->get('active_actor', 'Infra Director');
        $activeRole = $request->session()->get('active_role', $activeActor);

        $query = \App\Models\Notification::query()
            ->where(function ($q) use ($activeActor, $activeRole) {
                $q->where('target_actor', 'all')
                  ->orWhere('target_actor', $activeActor);
                if ($activeRole === 'Employee') {
                    $q->orWhere('target_actor', 'Employee');
                }
            });

        if ($activeRole === 'Infra Director') {
            $query->update(['read_by_director' => true]);
        } elseif ($activeRole === 'Project Manager') {
            $query->update(['read_by_manager' => true]);
        } else {
            $query->update(['read_by_employee' => true]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * @return Builder<Task>
     */
    private function filteredTasksQuery(Request $request): Builder
    {
        $query = Task::query();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }

        if ($request->filled('department')) {
            $query->where('responsible_department', $request->string('department'));
        }

        if ($request->filled('search')) {
            $term = '%'.$request->string('search').'%';
            $query->where(function (Builder $q) use ($term): void {
                $q->where('project_name', 'like', $term)
                    ->orWhere('task_description', 'like', $term)
                    ->orWhere('supplier_name', 'like', $term)
                    ->orWhere('task_given_to', 'like', $term)
                    ->orWhere('task_given_by', 'like', $term)
                    ->orWhere('responsible_department', 'like', $term)
                    ->orWhere('next_action', 'like', $term)
                    ->orWhere('remark', 'like', $term)
                    ->orWhere('status', 'like', $term);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->string('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->string('date_to'));
        }

        return $query;
    }

    private function canModifyTask(Task $task, string $activeActor): bool
    {
        if (ActorSession::canManageTasks()) {
            return true;
        }

        if (!ActorSession::isEmployee()) {
            return false;
        }

        return $task->task_given_to === $activeActor
            || ($task->task_given_to === 'Employee' && $activeActor === 'FEVEN');
    }
}
