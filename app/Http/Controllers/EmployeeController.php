<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Manager;
use App\Models\Task;
use App\Models\User;
use App\Support\ActorSession;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $this->ensureCanManageEmployees();

        $employees = Employee::orderBy('name')->get();
        $usersByName = User::with(['location', 'department'])->whereIn('name', $employees->pluck('name'))->get()->keyBy('name');
        $totalTasks = Task::count();
        $canDelete = ActorSession::isDirector();
        $locations = \App\Models\Location::orderBy('name')->get();
        $departments = \App\Models\Department::orderBy('name')->get();

        return view('employees.index', compact('employees', 'usersByName', 'totalTasks', 'canDelete', 'locations', 'departments'));
    }

    public function store(Request $request)
    {
        $this->ensureCanManageEmployees();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:4|confirmed',
            'location_id' => 'nullable|exists:locations,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $name = Employee::normalizeName($request->name);

        if (in_array($name, ['INFRA DIRECTOR', 'PROJECT MANAGER', 'DIRECTOR', 'COORDINATOR'], true)) {
            return back()->withErrors(['name' => 'This name is reserved for system roles.'])->withInput();
        }

        if (Employee::where('name', $name)->exists()) {
            return back()->withErrors(['name' => 'An employee with this name already exists.'])->withInput();
        }

        if (Manager::where('name', $name)->exists()) {
            return back()->withErrors(['name' => 'This name is already used by a manager.'])->withInput();
        }

        Employee::create(['name' => $name]);

        $email = strtolower(trim($request->email));

        User::updateOrCreate(
            ['name' => $name],
            [
                'email' => $email,
                'password' => $request->password,
                'must_change_password' => true,
                'location_id' => $request->location_id,
                'department_id' => $request->department_id,
            ]
        );

        return redirect()->back()->with(
            'success',
            "Employee {$name} added with login enabled. They can log in using username: '{$name}' or email: '{$email}'. Share the password you set — they must change it after first login."
        );
    }

    public function destroy(Employee $employee)
    {
        if (!ActorSession::isDirector()) {
            abort(403, 'Only the Infra Director can remove employees.');
        }

        if ($employee->name === 'FEVEN' && Employee::count() === 1) {
            return redirect()->back()->with('error', 'Cannot delete the last employee account.');
        }

        $hasTasks = Task::query()
            ->where('task_given_to', $employee->name)
            ->orWhere('task_given_by', $employee->name)
            ->exists();

        if ($hasTasks) {
            return redirect()->back()->with('error', "Cannot delete {$employee->name} — tasks are still assigned to this employee.");
        }

        User::where('name', $employee->name)->delete();
        $employee->delete();

        return redirect()->back()->with('success', "Employee {$employee->name} removed.");
    }

    private function ensureCanManageEmployees(): void
    {
        if (!ActorSession::canManageEmployees()) {
            abort(403, 'Only the Infra Director or Coordinator can manage employees.');
        }
    }
}
