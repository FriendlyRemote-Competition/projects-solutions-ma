<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancelledDeparture extends Model
{
    protected $table = 'cancelled_departures';

    protected $fillable = [
        'departure_code',
        'reason',
        'cancelled_at',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
    ];
}