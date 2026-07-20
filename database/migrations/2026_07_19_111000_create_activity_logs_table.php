<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('module')->index();
                $table->string('action');
                $table->text('description');
                
                // Polymorphic relation to the affected model
                $table->nullableMorphs('subject');
                
                // JSON blob for "Before/After" properties
                $table->json('properties')->nullable();
                
                // Security tracking
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                
                $table->string('status')->default('Success'); // Success, Failed
                $table->string('severity')->default('Information'); // Information, Warning, Important, Critical

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
