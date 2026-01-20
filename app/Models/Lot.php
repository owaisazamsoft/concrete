<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    
    protected $table = 'posts';
    // protected $fillable = ['name'];
    protected $guarded = [];

    protected $casts = [
        'data' => 'json',
    ];

    protected $attributes = [
        'type' => 'lot',
    ];
    
    
     protected static function booted()
    {
        static::addGlobalScope('type', function ($builder) {
            $builder->where('type', 'lot');
        });
    }



    
}
