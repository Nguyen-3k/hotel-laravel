<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['room_number', 'room_type', 'bed_type', 'price_per_night', 'image_url', 'description', 'status'];

    // Một phòng có thể có nhiều lượt đặt phòng lịch sử
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}