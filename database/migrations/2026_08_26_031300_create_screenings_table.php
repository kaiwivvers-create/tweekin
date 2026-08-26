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
        Schema::create('screenings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable(); // for guest users
            $table->enum('type', ['physical', 'mental', 'other']);
            $table->string('title'); // e.g. 'Fever or chills', 'Anxiety'
            $table->json('data'); // all the form responses
            $table->integer('severity')->nullable(); // 1-5 scale
            $table->string('assessment')->nullable(); // AI-generated summary
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screenings');
    }
};
