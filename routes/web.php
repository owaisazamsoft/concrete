<?php

use App\Models\DeliveryNote;
use App\Models\InvoiceSequence;
use App\Models\SaleInvoice;
use App\Models\SaleOrder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Broadcast::routes(['middleware' => ['auth:sanctum']]);
// ya agar session auth use kar rahe ho to
Broadcast::routes(['middleware' => ['auth']]);


Route::get('/test', function () {


    InvoiceSequence::where('type', 'delivery_note')->update([
            'last_number' => 0
    ]);
    foreach (DeliveryNote::all() as $key => $value) {
        $value->generatePrefix();
    }


    InvoiceSequence::where('type', 'sale_order')->update([
            'last_number' => 0
    ]);
    foreach (SaleOrder::all() as $key => $value) {
        $value->generatePrefix();
    }

     InvoiceSequence::where('type', 'sale_invoice')->update([
            'last_number' => 0
    ]);
    foreach (SaleInvoice::all() as $key => $value) {
        $value->generatePrefix();
    }

    
    
});


Route::get('/{any?}', function () {
    return view('index');
})->where('any', '.*');

