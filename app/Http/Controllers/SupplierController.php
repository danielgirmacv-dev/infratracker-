<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Task;
use App\Imports\SuppliersImport;
use App\Support\ActorSession;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureDirector();

        $suppliers = Supplier::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->string('search').'%');
            })
            ->orderBy('name')
            ->get();
        $totalTasks = Task::count();
        return view('suppliers.index', compact('suppliers', 'totalTasks'));
    }

    public function store(Request $request)
    {
        $this->ensureDirector();

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
        $this->ensureDirector();

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        Excel::import(new SuppliersImport, $request->file('file'));

        return redirect()->back()->with('success', 'Supplier names imported successfully from Excel.');
    }

    public function destroy(Supplier $supplier)
    {
        $this->ensureDirector();

        $supplier->delete();
        return redirect()->back()->with('success', 'Supplier Name deleted.');
    }

    private function ensureDirector(): void
    {
        if (!ActorSession::isDirector()) {
            abort(403, 'Unauthorized.');
        }
    }
}
