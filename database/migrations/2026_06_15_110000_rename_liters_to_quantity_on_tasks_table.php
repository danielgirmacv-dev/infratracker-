<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tasks', 'liters')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->decimal('quantity', 12, 2)->nullable()->after('amount');
                $table->string('quantity_unit', 20)->nullable()->after('quantity');
            });

            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('liters', 'quantity');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->string('quantity_unit', 20)->nullable()->after('quantity');
        });

        DB::table('tasks')
            ->whereNotNull('quantity')
            ->whereNull('quantity_unit')
            ->update(['quantity_unit' => 'Iitter']);
    }

    public function down(): void
    {
        if (!Schema::hasColumn('tasks', 'quantity')) {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('quantity_unit');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('quantity', 'liters');
        });
    }
};
