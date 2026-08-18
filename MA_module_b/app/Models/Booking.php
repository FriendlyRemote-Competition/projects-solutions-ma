<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';
    
    protected $primaryKey = 'booking_code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'booking_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'seats',
        'status',
        'fare_cny',
        'total_fare_cny',
        'departure_code',
        'cancelled_at',
    ];

    protected $casts = [
        'seats' => 'integer',
        'fare_cny' => 'decimal:2',
        'total_fare_cny' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];
}