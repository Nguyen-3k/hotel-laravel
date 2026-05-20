<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
{
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('room_id')->constrained()->onDelete('cascade'); // Khóa ngoại liên kết bảng rooms
        $table->string('customer_name');        // Tên khách hàng đặt phòng
        $table->string('customer_phone');       // Số điện thoại khách
        $table->date('check_in_date');          // Ngày nhận phòng
        $table->date('check_out_date');         // Ngày trả phòng
        $table->integer('total_price');         // Tổng số tiền phải trả
        $table->string('status')->default('pending'); // Trạng thái đơn: pending, confirmed, cancelled
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
