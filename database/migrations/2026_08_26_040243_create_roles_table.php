<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // super_admin, admin, moderator, user
            $table->string('label'); // Display name
            $table->json('permissions')->nullable(); // ['users.view', 'users.edit', 'screenings.view', 'settings.edit', etc.]
            $table->integer('level')->default(0); // Hierarchy level (higher = more power)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
