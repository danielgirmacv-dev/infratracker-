<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class Manager extends Model
{
    protected $fillable = ['name'];

    public static function names(): Collection
    {
        if (!Schema::hasTable('managers')) {
            return collect();
        }

        return static::query()->orderBy('name')->pluck('name');
    }

    public static function assigneeOptions(): array
    {
        return static::names()->all();
    }

    public static function isManagerName(string $name): bool
    {
        if ($name === 'Project Manager') {
            return true;
        }

        if (!Schema::hasTable('managers')) {
            return false;
        }

        return static::query()->where('name', $name)->exists();
    }

    public static function normalizeName(string $name): string
    {
        return strtoupper(trim($name));
    }
}
