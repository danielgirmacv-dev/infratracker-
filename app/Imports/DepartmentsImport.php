<?php

namespace App\Imports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class DepartmentsImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $name = trim($row['department_name'] ?? $row['name'] ?? '');
        if (empty($name)) return null;

        // Skip duplicates
        if (Department::where('name', $name)->exists()) return null;

        return new Department([
            'name' => $name,
        ]);
    }
}
