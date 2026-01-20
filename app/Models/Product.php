<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{


    protected $table = 'posts';
    // protected $fillable = ['name'];
    protected $guarded = [];

    protected $attributes = [
        'type' => 'product',
    ];

    
     protected static function booted()
    {
        static::addGlobalScope('type', function ($builder) {
            $builder->where('type', 'product');
        });
    }



    
}
