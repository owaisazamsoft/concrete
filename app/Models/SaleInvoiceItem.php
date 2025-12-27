<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleInvoiceItem extends Model
{

    protected $table = 'sale_invoice_items'; 

    protected $guarded = [];


    public function deliveryNote() {
        return $this->belongsTo(DeliveryNote::class, 'delivery_note_id');
    }

  

    
}
