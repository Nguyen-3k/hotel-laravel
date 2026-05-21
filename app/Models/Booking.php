<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['room_id', 'customer_name', 'customer_phone', 'customer_email', 'check_in_date', 'check_out_date', 'total_price', 'status'];

    // Một đơn đặt phòng bắt buộc phải thuộc về một phòng cụ thể
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}