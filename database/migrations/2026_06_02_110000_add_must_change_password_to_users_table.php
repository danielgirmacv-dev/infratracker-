<?php

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('must_change_password')->default(false)->after('password');
        });

        if (!Schema::hasTable('employees')) {
            return;
        }

        foreach (Employee::all() as $employee) {
            User::firstOrCreate(
                ['name' => $employee->name],
                [
                    'email' => strtolower(str_replace(' ', '', $employee->name)).'@infratracker.local',
                    'password' => 'employee123',
                    'must_change_password' => true,
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('must_change_password');
        });
    }
};
