<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class Employee extends Model
{
    protected $fillable = ['name'];

    public static function names(): Collection
    {
        if (!Schema::hasTable('employees')) {
            return collect(['FEVEN']);
        }

        return static::query()->orderBy('name')->pluck('name');
    }

    public static function assigneeOptions(): array
    {
        return static::names()->all();
    }

    public static function isEmployeeName(string $name): bool
    {
        if ($name === 'Employee') {
            return true;
        }

        if (!Schema::hasTable('employees')) {
            return $name === 'FEVEN';
        }

        return static::query()->where('name', $name)->exists();
    }

    public static function normalizeName(string $name): string
    {
        return strtoupper(trim($name));
    }
}
