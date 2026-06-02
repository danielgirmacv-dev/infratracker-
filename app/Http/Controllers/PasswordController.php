<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Support\ActorSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function showChangeForm()
    {
        $totalTasks = Task::count();

        return view('settings.password', compact('totalTasks'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:4|confirmed',
        ]);

        $activeActor = session('active_actor');
        if (!$activeActor) {
            return redirect()->route('login');
        }

        $defaultPasswords = [
            'Infra Director' => 'director123',
            'Project Manager' => 'manager123',
        ];

        $defaultPassword = $defaultPasswords[$activeActor] ?? 'employee123';

        $user = User::firstOrCreate(
            ['name' => $activeActor],
            [
                'email' => strtolower(str_replace(' ', '', $activeActor)).'@example.com',
                'password' => Hash::make($defaultPassword),
            ]
        );

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match our records.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }
}
