<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Imports\ProjectsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('name')->get();
        $totalTasks = Task::count();
        return view('projects.index', compact('projects', 'totalTasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:projects,name',
        ]);

        Project::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Project Name added successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        Excel::import(new ProjectsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Project names imported successfully from Excel.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->back()->with('success', 'Project Name deleted.');
    }
}
