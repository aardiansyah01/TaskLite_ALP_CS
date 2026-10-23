<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique();
            $table->string('google_id')->nullable()->unique();

            $table->string('role')->default('member');

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->unsignedTinyInteger('clearance_level')
                ->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropUnique(['username']);
            $table->dropUnique(['google_id']);

            $table->dropColumn([
                'username',
                'google_id',
                'role',
                'clearance_level',
            ]);
        });
    }
};
