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
        Schema::table('bookings', function (Blueprint $table) {
            // Thêm cột user_id, cho phép null (để khách vãng lai không có tài khoản vẫn đặt được)
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};
