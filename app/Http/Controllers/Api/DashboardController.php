<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\AuctionPlatform;
use App\Models\Auctions;
use App\Models\DeliveryNote;
use App\Models\Product;
use App\Models\SaleInvoice;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{


        public function counters()
    {


        return response()->json([
            'user' => User::count() - 1,
            'product' => Product::count(),
            'dc' => DeliveryNote::count(),
            'invoice' => SaleInvoice::count(),
        ],200);
   
    }






}

