<?php


namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'email',
        'password',
        'name',
        'role',
        'is_active',
        'api_token',
    ];

    protected $hidden = [
        'password',
        'api_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}