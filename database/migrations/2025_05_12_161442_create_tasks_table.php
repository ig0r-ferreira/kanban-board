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
            $table->id();
            $table->string('key');
            $table->string('title');
            $table->string('description');
            $table->string('priority')->default('Medium');
            $table->foreignId('status_id')->constrained();
            $table->foreignId('reporter_id')->constrained('users');
            $table->foreignId('assignee_id')->constrained('users');
            $table->dateTime('due_date', precision: 0);
            $table->dateTime('resolution_date', precision: 0)->nullable();
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
