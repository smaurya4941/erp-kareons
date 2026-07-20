<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            
            // Text Fields
            $table->text('today_summary')->nullable();
            $table->text('problems_faced')->nullable();
            $table->text('tomorrow_plan')->nullable();
            
            // Workflow
            $table->enum('status', ['Draft', 'Submitted', 'Reviewed'])->default('Draft');
            
            // Stats Snapshot (Frozen at generation)
            $table->json('stats_snapshot')->nullable();
            
            $table->timestamps();

            // Rule 1: Only one report per MR per day
            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
