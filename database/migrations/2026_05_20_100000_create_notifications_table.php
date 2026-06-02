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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->string('actor', 255);
            $table->string('type', 255);
            $table->text('message');
            $table->string('target_actor', 255)->default('all');
            $table->boolean('read_by_director')->default(false);
            $table->boolean('read_by_manager')->default(false);
            $table->boolean('read_by_employee')->default(false);
            $table->timestamps();

            $table->foreign('task_id')
                ->references('item_no')
                ->on('tasks')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
