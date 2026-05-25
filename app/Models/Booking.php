<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
protected $fillable = [
        'user_id', 'room_id', 'customer_name', 'customer_phone', 'customer_email', 
        'check_in_date', 'check_out_date', 'total_price', 'status',
        'bank_info', 'refund_qr', 'refund_reason',
        'guest_count', 'room_count', 'surcharge' // Thêm 3 trường này
    ];
    // Một đơn đặt phòng bắt buộc phải thuộc về một phòng cụ thể
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}