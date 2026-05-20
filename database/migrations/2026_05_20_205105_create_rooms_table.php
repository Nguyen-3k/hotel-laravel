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
    Schema::create('rooms', function (Blueprint $table) {
        $table->id();
        $table->string('room_number')->unique(); // Số phòng (VD: P101, P102)
        $table->string('room_type');             // Loại phòng (VD: VIP, Standard, Deluxe)
        $table->integer('price_per_night');      // Giá tiền một đêm
        $table->string('image_url')->nullable(); // Đường dẫn ảnh phòng
        $table->text('description')->nullable(); // Mô tả chi tiết phòng
        $table->string('status')->default('available'); // Trạng thái: available hoặc booked
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
