<?php

use App\Http\Controllers\Api\DummyController;
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



Route::get('/data', [DummyController::class,'start']);




Route::get('/{any?}', function () {
    return view('index');
})->where('any', '.*');

