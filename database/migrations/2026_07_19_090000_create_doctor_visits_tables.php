<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Doctor Visits Table
        Schema::create('doctor_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // The MR
            
            // Location Data
            $table->date('date');
            $table->time('time');
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->integer('accuracy')->nullable();
            $table->text('address')->nullable();
            
            // Doctor Details (Manual Entry for V1)
            $table->string('doctor_name');
            $table->string('clinic_name')->nullable();
            $table->string('specialization');
            $table->string('phone')->nullable();
            $table->string('area')->nullable();
            $table->text('doctor_address')->nullable();
            
            // Discussion
            $table->text('discussion_summary');
            $table->string('doctor_response'); // e.g. Interested, Not Interested
            $table->string('competitor_medicines')->nullable();
            $table->text('remarks')->nullable();
            
            // Status
            $table->string('status')->default('Completed'); // Later versions can have 'Draft'
            
            $table->timestamps();
        });

        // 2. Doctor Visit Products (Pivot)
        Schema::create('doctor_visit_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_visit_id')->constrained('doctor_visits')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('interest_level')->nullable(); // High, Medium, Low
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            $table->unique(['doctor_visit_id', 'product_id']);
        });

        // 3. Doctor Visit Samples (Pivot)
        Schema::create('doctor_visit_samples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_visit_id')->constrained('doctor_visits')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
            
            $table->unique(['doctor_visit_id', 'product_id']);
        });

        // 4. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_visit_id')->constrained('doctor_visits')->onDelete('cascade');
            $table->string('doctor_name');
            $table->string('status')->default('Pending');
            $table->decimal('total_amount', 10, 2)->default(0); // If pricing is added later
            $table->timestamps();
        });

        // 5. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['order_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('doctor_visit_samples');
        Schema::dropIfExists('doctor_visit_products');
        Schema::dropIfExists('doctor_visits');
    }
};
