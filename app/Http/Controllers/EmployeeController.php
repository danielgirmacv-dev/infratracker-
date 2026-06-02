<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Task;
use App\Models\User;
use App\Support\ActorSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $this->ensureDirector();

        $employees = Employee::orderBy('name')->get();
        $totalTasks = Task::count();

        return view('employees.index', compact('employees', 'totalTasks'));
    }

    public function store(Request $request)
    {
        $this->ensureDirector();

        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $name = Employee::normalizeName($request->name);

        if (in_array($name, ['INFRA DIRECTOR', 'PROJECT MANAGER', 'DIRECTOR', 'COORDINATOR'], true)) {
            return back()->withErrors(['name' => 'This name is reserved for system roles.'])->withInput();
        }

        if (Employee::where('name', $name)->exists()) {
            return back()->withErrors(['name' => 'An employee with this name already exists.'])->withInput();
        }

        Employee::create(['name' => $name]);

        User::firstOrCreate(
            ['name' => $name],
            [
                'email' => strtolower(str_replace(' ', '', $name)).'@example.com',
                'password' => Hash::make('employee123'),
            ]
        );

        return redirect()->back()->with('success', "Employee {$name} added. Default password: employee123");
    }

    public function destroy(Employee $employee)
    {
        $this->ensureDirector();

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

    private function ensureDirector(): void
    {
        if (!ActorSession::isDirector()) {
            abort(403, 'Only the Infra Director can manage employees.');
        }
    }
}
