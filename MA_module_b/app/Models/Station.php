<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    protected $table = 'stations';
    
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'bank',
        'district',
        'address',
    ];

    public function linesAsStationA(): HasMany
    {
        return $this->hasMany(Line::class, 'station_a_code', 'code');
    }

    public function linesAsStationB(): HasMany
    {
        return $this->hasMany(Line::class, 'station_b_code', 'code');
    }
}