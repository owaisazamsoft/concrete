<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    
    protected $table = 'delivery_notes'; 
    protected $fillable = [
        'date',
        'ref',
        'remarks',
        'status',
        'total',
        'sale_order_id',
        'user_id',
        'created_at',
        'updated_at',
     ];


    public function items() {
        return $this->hasMany(DeliveryNoteItem::class, 'delivery_note_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function saleOrder() {
        return $this->belongsTo(SaleOrder::class, 'sale_order_id');
    }

    public function saleInvoiceItem() {
        return $this->belongsTo(SaleInvoiceItem::class, 'delivery_note_id');
    }
    

}
