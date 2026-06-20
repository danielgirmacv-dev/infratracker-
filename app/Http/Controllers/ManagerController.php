<?php

namespace App\Http\Controllers;

use App\Models\Manager;
use App\Models\Task;
use App\Models\User;
use App\Support\ActorSession;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function index()
    {
        $this->ensureDirector();

        $managers = Manager::orderBy('name')->get();
        $usersByName = User::with(['location', 'department'])->whereIn('name', $managers->pluck('name'))->get()->keyBy('name');
        $totalTasks = Task::count();
        $locations = \App\Models\Location::orderBy('name')->get();
        $departments = \App\Models\Department::orderBy('name')->get();

        return view('managers.index', compact('managers', 'usersByName', 'totalTasks', 'locations', 'departments'));
    }

    public function store(Request $request)
    {
        $this->ensureDirector();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:4|confirmed',
            'location_id' => 'nullable|exists:locations,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $name = Manager::normalizeName($request->name);

        if (in_array($name, ['INFRA DIRECTOR', 'PROJECT MANAGER', 'DIRECTOR', 'COORDINATOR'], true)) {
            return back()->withErrors(['name' => 'This name is reserved for system roles.'])->withInput();
        }

        if (Manager::where('name', $name)->exists()) {
            return back()->withErrors(['name' => 'A manager with this name already exists.'])->withInput();
        }

        Manager::create(['name' => $name]);

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
            "Manager {$name} added with login enabled. They can log in using username: '{$name}' or email: '{$email}'. Share the password you set — they must change it after first login."
        );
    }

    public function destroy(Manager $manager)
    {
        $this->ensureDirector();

        $hasTasks = Task::query()
            ->where('task_given_to', $manager->name)
            ->orWhere('task_given_by', $manager->name)
            ->exists();

        if ($hasTasks) {
            return redirect()->back()->with('error', "Cannot delete {$manager->name} — tasks are still assigned to this manager.");
        }

        User::where('name', $manager->name)->delete();
        $manager->delete();

        return redirect()->back()->with('success', "Manager {$manager->name} removed.");
    }

    public function update(Request $request, Manager $manager)
    {
        $this->ensureDirector();

        $user = User::where('name', $manager->name)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:4|confirmed',
            'location_id' => 'nullable|exists:locations,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $oldName = $manager->name;
        $newName = Manager::normalizeName($request->name);

        if (in_array($newName, ['INFRA DIRECTOR', 'PROJECT MANAGER', 'DIRECTOR', 'COORDINATOR'], true)) {
            return back()->withErrors(['name' => 'This name is reserved for system roles.'])->withInput();
        }

        if ($newName !== $oldName && Manager::where('name', $newName)->exists()) {
            return back()->withErrors(['name' => 'A manager with this name already exists.'])->withInput();
        }

        if ($newName !== $oldName) {
            Task::where('task_given_to', $oldName)->update(['task_given_to' => $newName]);
            Task::where('task_given_by', $oldName)->update(['task_given_by' => $newName]);
            \App\Models\Notification::where('actor', $oldName)->update(['actor' => $newName]);
            \App\Models\Notification::where('target_actor', $oldName)->update(['target_actor' => $newName]);
        }

        $manager->update(['name' => $newName]);

        $userData = [
            'name' => $newName,
            'email' => strtolower(trim($request->email)),
            'location_id' => $request->location_id,
            'department_id' => $request->department_id,
        ];

        if ($request->filled('password')) {
            $userData['password'] = $request->password;
        }

        $user->update($userData);

        return redirect()->back()->with('success', "Manager updated successfully.");
    }

    private function ensureDirector(): void
    {
        if (!ActorSession::isDirector()) {
            abort(403, 'Only the Infra Director or Coordinator can manage managers.');
        }
    }
}
