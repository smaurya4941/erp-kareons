<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add remarks to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('status');
        });

        // 2. Create Order Status Histories table
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('changed_by_user_id')->constrained('users')->onDelete('cascade');
            $table->string('status'); // Pending, Reviewed, Completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
