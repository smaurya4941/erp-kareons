<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sample_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // The MR
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            // Action type: assigned, increased, reduced, returned, adjustment, distributed
            $table->string('type'); 
            
            // The amount changed (e.g. +20, -5). Signed integer.
            $table->integer('quantity'); 
            
            // Reason for adjustment/return
            $table->string('reason')->nullable();
            
            // The Admin who performed this action (nullable if performed by system/MR via distribution)
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_transactions');
    }
};
