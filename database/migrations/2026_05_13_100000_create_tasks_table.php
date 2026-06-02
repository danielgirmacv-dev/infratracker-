<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('item_no');
            $table->date('date');
            $table->string('project_name', 255);
            $table->text('task_description');
            $table->string('supplier_name', 255)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['Pending', 'In Progress', 'Completed', 'On Hold']);
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical']);
            $table->unsignedTinyInteger('progress');
            $table->string('next_action', 255)->nullable();
            $table->string('responsible_department', 255);
            $table->string('task_given_by', 255)->default('Infra Director');
            $table->string('task_given_to', 255);
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
