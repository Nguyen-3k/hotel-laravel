<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model {
    protected $fillable = ['user_id', 'email', 'coupon_code', 'booking_id'];
}