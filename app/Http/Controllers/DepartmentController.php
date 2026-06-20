<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Task;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->string('search').'%');
            })
            ->orderBy('name')
            ->get();
        $totalTasks = Task::count();
        return view('departments.index', compact('departments', 'totalTasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
        ]);

        Department::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Department added successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\DepartmentsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Departments imported successfully from Excel.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->back()->with('success', 'Department deleted.');
    }
}
