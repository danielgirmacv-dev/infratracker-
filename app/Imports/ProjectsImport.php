<?php

namespace App\Imports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class ProjectsImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $name = trim($row['project_name'] ?? $row['name'] ?? '');
        if (empty($name)) return null;

        // Skip duplicates
        if (Project::where('name', $name)->exists()) return null;

        return new Project([
            'name' => $name,
        ]);
    }
}
