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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique();
            $table->string('name')->unique();
            $table->string('category');
            $table->string('strength');
            $table->string('pack_size');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true); // true = active, false = inactive
            
            // Future-ready fields
            $table->string('brand')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('hsn_code')->nullable();
            $table->decimal('mrp', 10, 2)->nullable();
            $table->decimal('gst', 5, 2)->nullable();
            $table->string('barcode')->nullable();
            $table->boolean('expiry_required')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
