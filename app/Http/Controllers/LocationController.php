<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Task;
use App\Support\ActorSession;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureDirector();

        $locations = Location::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->string('search').'%');
            })
            ->orderBy('name')
            ->get();
        $totalTasks = Task::count();
        return view('locations.index', compact('locations', 'totalTasks'));
    }

    public function store(Request $request)
    {
        $this->ensureDirector();

        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name',
        ]);

        Location::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Location added successfully.');
    }

    public function import(Request $request)
    {
        $this->ensureDirector();

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\LocationsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Locations imported successfully from Excel.');
    }

    public function destroy(Location $location)
    {
        $this->ensureDirector();

        $location->delete();
        return redirect()->back()->with('success', 'Location deleted.');
    }

    private function ensureDirector(): void
    {
        if (!ActorSession::isDirector()) {
            abort(403, 'Unauthorized.');
        }
    }
}
