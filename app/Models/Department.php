<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{


    protected $table = 'posts';
    // protected $fillable = ['name'];
    protected $guarded = [];

    protected $attributes = [
        'type' => 'department',
    ];

    
     protected static function booted()
    {
        static::addGlobalScope('type', function ($builder) {
            $builder->where('type', 'department');
        });
    }



    
}
