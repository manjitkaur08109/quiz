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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('quiz_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('score');
            $table->integer('passing_score')->default(0);
            $table->integer('total_questions')->default(0);
            $table->integer('marks_obtained')->default(0);
            $table->json('attempted_answers')->nullable();
            $table->timestamps();

            $table->foreign('quiz_id')
                  ->references('id')
                  ->on('quiz')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
