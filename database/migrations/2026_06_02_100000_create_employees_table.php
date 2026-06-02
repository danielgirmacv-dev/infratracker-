<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        DB::table('employees')->insert([
            'name' => 'FEVEN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('tasks')->where('task_given_to', 'Employee')->update(['task_given_to' => 'FEVEN']);
        DB::table('tasks')->where('task_given_by', 'Employee')->update(['task_given_by' => 'FEVEN']);
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
