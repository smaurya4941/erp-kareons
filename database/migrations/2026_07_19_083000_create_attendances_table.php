<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Core Date
            $table->date('date'); // Ensure one record per user per day
            
            // Check-in Info
            // NOTE: use datetime (not timestamp). A NOT NULL timestamp column implicitly
            // gets "ON UPDATE CURRENT_TIMESTAMP" under MySQL's default
            // explicit_defaults_for_timestamp=0, which would reset check_in_time on every update.
            $table->dateTime('check_in_time');
            $table->string('check_in_selfie');
            $table->decimal('check_in_lat', 10, 8)->nullable();
            $table->decimal('check_in_lng', 11, 8)->nullable();
            $table->integer('check_in_accuracy')->nullable(); // in meters
            $table->text('check_in_address')->nullable();
            $table->json('check_in_device_info')->nullable();
            
            // Check-out Info (Nullable until check out)
            $table->dateTime('check_out_time')->nullable();
            $table->string('check_out_selfie')->nullable();
            $table->decimal('check_out_lat', 10, 8)->nullable();
            $table->decimal('check_out_lng', 11, 8)->nullable();
            $table->integer('check_out_accuracy')->nullable();
            $table->text('check_out_address')->nullable();
            $table->json('check_out_device_info')->nullable();
            
            // Calculated Fields & Status
            $table->integer('working_minutes')->nullable(); // calculated on check-out
            $table->enum('status', ['Present', 'Incomplete', 'Absent'])->default('Incomplete');
            $table->boolean('is_late')->default(false); // flagged if check-in is past threshold

            $table->timestamps();
            
            // Unique constraint: one attendance per user per day
            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
