<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sample_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // The MR
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('assigned_quantity')->default(0);
            $table->integer('distributed_quantity')->default(0);
            $table->timestamps();
            
            // One product per MR in this live balance table
            $table->unique(['user_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_assignments');
    }
};
