<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleOrder extends Model
{

    protected $table = 'sale_orders'; 

    protected $fillable = [
        'date',
        'ref',
        'remarks',
        'status',
        'subtotal',
        'tax',
        'discount',
        'total',
        'user_id',
        'created_at',
        'updated_at'
    ];


    public function items() {
        return $this->hasMany(SaleOrderItem::class, 'sale_order_id');
    }

    public function saleOrderItem() {
        return $this->hasMany(SaleOrderItem::class, 'sale_order_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deliveryNote() {
        return $this->hasMany(DeliveryNote::class, 'sale_order_id');
    }

    public function generatePrefix()
    {

        $sequence = InvoiceSequence::where('type', 'sale_order')
            ->lockForUpdate()
            ->first();
        $nextNumber =  $sequence->incrementSequence();
        $this->prefix = 'SO-'. str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $this->save();
    }
    
    
}
