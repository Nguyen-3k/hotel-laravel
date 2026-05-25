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
    public function images() {
        return $this->hasMany(RoomImage::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }
    
    // Hàm tính điểm đánh giá trung bình
    public function averageRating() {
        return $this->reviews()->avg('rating') ?: 0;
    }
}