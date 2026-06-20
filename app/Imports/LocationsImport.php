<?php

namespace App\Imports;

use App\Models\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class LocationsImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $name = trim($row['location_name'] ?? $row['name'] ?? '');
        if (empty($name)) return null;

        // Skip duplicates
        if (Location::where('name', $name)->exists()) return null;

        return new Location([
            'name' => $name,
        ]);
    }
}
