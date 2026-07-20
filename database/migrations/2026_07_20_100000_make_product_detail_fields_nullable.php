<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Admin only needs to enter the product name for now; every other detail
     * can be filled in later, so relax the NOT NULL constraints.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->nullable()->change();
            $table->string('strength')->nullable()->change();
            $table->string('pack_size')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->nullable(false)->change();
            $table->string('strength')->nullable(false)->change();
            $table->string('pack_size')->nullable(false)->change();
        });
    }
};
