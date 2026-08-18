<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Line extends Model
{
    protected $table = 'lines';
    
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'status',
        'station_a_code',
        'station_b_code',
        'seat_capacity',
        'crossing_minutes',
        'fare_cny',
    ];

    protected $casts = [
        'seat_capacity' => 'integer',
        'crossing_minutes' => 'integer',
        'fare_cny' => 'decimal:2',
    ];

    public function stationA(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'station_a_code', 'code');
    }

    public function stationB(): BelongsTo
    {
        return $this->belongsTo(Station::class, 'station_b_code', 'code');
    }

    public function serviceWindows(): HasMany
    {
        return $this->hasMany(ServiceWindow::class, 'line_code', 'code');
    }
}