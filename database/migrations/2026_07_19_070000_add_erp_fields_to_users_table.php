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
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_code')->unique()->nullable()->after('id');
            $table->string('photo')->nullable()->after('email');
            $table->string('mobile')->unique()->nullable()->after('photo');
            $table->text('address')->nullable()->after('mobile');
            $table->date('joining_date')->nullable()->after('address');
            $table->boolean('status')->default(true)->after('joining_date');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_code',
                'photo',
                'mobile',
                'address',
                'joining_date',
                'status'
            ]);
            $table->dropSoftDeletes();
        });
    }
};
