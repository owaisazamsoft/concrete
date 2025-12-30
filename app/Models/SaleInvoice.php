<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SaleInvoice extends Model
{

    protected $table = 'sale_invoices'; 

     protected $guarded = [];


    public function items() {
        return $this->hasMany(SaleInvoiceItem::class, 'sale_invoice_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }


      public function generatePrefix()
    {

        $sequence = InvoiceSequence::where('type', 'sale_invoice')
            ->lockForUpdate()
            ->first();
        $nextNumber =  $sequence->incrementSequence();
        $this->prefix = 'SI-'. str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $this->save();
    }
    

}
