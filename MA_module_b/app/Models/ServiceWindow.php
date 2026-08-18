<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceWindow extends Model
{
    protected $table = 'service_windows';

    protected $fillable = [
        'line_code',
        'start_time',
        'end_time',
        'interval_minutes',
    ];

    protected $casts = [
        'interval_minutes' => 'integer',
    ];

    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class, 'line_code', 'code');
    }
}