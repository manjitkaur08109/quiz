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

            // ✅ Match this to your quiz table ID type
            $table->uuid('quiz_id');

            // ✅ Match this to users.id (usually bigint)
            $table->unsignedBigInteger('user_id');

            $table->integer('score');
            $table->integer('total_questions')->default(0);
             // ➕ Add new columns
             $table->json('correct_answers')->nullable();
             $table->integer('marks_obtained')->default(0);
            $table->timestamps();

            // ✅ Foreign Keys
            $table->foreign('quiz_id')
                  ->references('id')
                  ->on('quiz') // ✅ correct table name
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
