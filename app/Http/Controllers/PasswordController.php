<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Support\ActorSession;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    public function showChangeForm()
    {
        $totalTasks = Task::count();
        $mustChangePassword = false;

        if (ActorSession::isEmployee()) {
            $mustChangePassword = (bool) User::where('name', ActorSession::name())->value('must_change_password');
        }

        return view('settings.password', compact('totalTasks', 'mustChangePassword'));
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

        $user = User::where('name', $activeActor)->first();

        if (!$user) {
            return back()->withErrors(['current_password' => 'Account not found. Contact your director.']);
        }

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => $request->new_password,
            'must_change_password' => false,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Password updated successfully.');
    }
}
