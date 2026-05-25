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
            $table->integer('guest_count')->default(1)->after('customer_email');
            $table->integer('room_count')->default(1)->after('guest_count');
            $table->integer('surcharge')->default(0)->after('total_price'); // Lưu tiền phụ thu
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
