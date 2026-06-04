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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('overall_score', 4, 2); // Overall score out of 10
            $table->decimal('percentage', 5, 2); // Score converted to percentage
            $table->integer('duration_seconds'); // Total duration spent in seconds
            $table->text('summary_feedback')->nullable();
            $table->text('ai_suggestions')->nullable(); // General suggestions for improvement
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
