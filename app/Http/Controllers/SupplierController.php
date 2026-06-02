<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Task;
use App\Imports\SuppliersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $totalTasks = Task::count();
        return view('suppliers.index', compact('suppliers', 'totalTasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name',
        ]);

        Supplier::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Supplier Name added successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        Excel::import(new SuppliersImport, $request->file('file'));

        return redirect()->back()->with('success', 'Supplier names imported successfully from Excel.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->back()->with('success', 'Supplier Name deleted.');
    }
}
