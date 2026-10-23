<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();

            $table->foreignId('department_id')
                ->constrained('departments')
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('assigned_to')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('status', [
                'todo',
                'in_progress',
                'completed',
            ])->default('todo');

            $table->unsignedTinyInteger('classification')
                ->default(1);

            $table->date('due_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
