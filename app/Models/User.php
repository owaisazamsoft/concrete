<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;  // Ensure this is included
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable  // This should extend Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'data',

    ];

    protected $appends = [
        // 'image_preview',
    ];

    protected $casts = [
        'data' => 'json',
    ];


    protected $hidden = [
        'password',
    ];

  
 

}
