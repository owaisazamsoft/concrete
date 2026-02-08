<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

use App\Models\User;
use App\Services\ReportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{

        public function customerLedger(Request $request)
    {  
        
        $length = $request->input('length', 50);
        $page   = $request->input('page', 1);
        $offset = ($page - 1) * $length;

        $baseQuery = User::query()->where('user_type','!=',1)
        ->when($request->group, function ($q, $value) {
            $q->where('users.group',$value);
        })
        ->when($request->search, function ($q, $search) {
            $q->where(function($q) use ($search){
                $q->where('users.firstName','like',"%{$search}%")
                ->orWhere('users.companyAddress1','like',"%{$search}%")
                ->orWhere('users.phone','like',"%{$search}%")
                ->orWhere('users.id','like',"%{$search}%");
            });
        });


        // ✅ Clone the query before using count()
        $count = (clone $baseQuery)->count();
        $data = $baseQuery->select([
                'users.*',

              

                 DB::raw("(SELECT SUM(credit) FROM payments WHERE payments.user_id = users.id ) AS payments_credit"),

                 DB::raw("(SELECT SUM(debit) FROM payments WHERE payments.user_id = users.id ) AS payments_debit"),

                 DB::raw("(SELECT SUM(total) FROM delivery_notes WHERE delivery_notes.user_id = users.id ) AS deliveryNote")
                   
                ])
                ->orderByDesc('id')
                ->skip($offset)
                ->take($length)
                ->get()
                ->map(function($item){

                  
                    $balance = 0;
                
                    $balance =  $balance + $item->payments_credit;
                    $balance =  $balance - $item->payments_debit;
                    $balance =  $balance - $item->deliveryNote;
                    $item->balance = $balance;


                    return $item;

            });

            return response()->json([
                'total' => $count,
                'page' => $page,
                'offset' => $offset,
                'from' => $count > 0 ? $offset + 1 : 0,
                'to'   =>  $offset + count($data),
                'last_page' => ceil($count / $length),
                'data' => $data,
            ]);
    }



        public function customerLedgerDetail(Request $request,$id)
    {  

        $model = User::find($id);
        $balance = 0;

        $baseQuery = ReportService::getCustomerLeder($id);
        $query = DB::query()->fromSub($baseQuery, 'transactions');

        // ✅ Clone the query before using count()
        $count = (clone $query)->count();
        $data = $query->select([
                '*'                       
            ])
            ->orderBy('date')
            ->get()
            ->map(function($item) use(&$balance) {

                $item->date = date('d-M-Y', strtotime($item->date));
                // Convert values to numbers
                $credit = floatval($item->credit);
                $debit = floatval($item->debit);

                // Calculate running balance
                $balance += $credit;
                $balance -= $debit;
                $item->balance = floatval($balance);
                return $item;
            });



        // 1. Filter by Start Date
        if ($request->filled('from_date')) {
            $data = $data->filter(function ($item) use ($request) {
                return date('Y-m-d', strtotime($item->date)) >= $request->from_date;
            })->values();
        }

        // 2. Filter by End Date
        if ($request->filled('to_date')) {
            $data = $data->filter(function ($item) use ($request) {
                return date('Y-m-d', strtotime($item->date)) <= $request->to_date;
            })->values();
        }

        // 3. Filter by Search Text
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $data = $data->filter(function ($item) use ($search) {
                return str_contains(strtolower($item->remarks ?? ''), $search);
            })->values();
        }

        $length = $request->input('length', 20);
        $page   = $request->input('page', 1);
        $total = $count;
        $offset = ($page - 1) * $length;
        $data = $data->slice($offset, $length)->values();


        return response()->json([
            'total' => $total,
            'page' => $page,
            'offset' => $offset,
            'last_page' => ceil($total / $length),
            'from' => $count > 0 ? $offset + 1 : 0,
            'to'   =>  $offset + count($data),
            'data' => $data,
            'balance' => $balance,
            'customer' => $model,
        ]);


    }


        public function inventory(Request $request)
    {  

        $length = $request->input('length', 50);
        $page   = $request->input('page', 1);
        $offset = ($page - 1) * $length;

            $baseQuery = Product::leftJoin('unit','unit.id','=','products.unit_id')
            ->leftJoin('category','category.id','=','products.category_id')
            ->when($request->search, function ($q, $search) {
                $q->where(function($q) use ($search){
                    $q->where('products.title','like',"%{$search}%")
                    ->orWhere('products.sku','like',"%{$search}%")
                    ->orWhere('category.title','like',"%{$search}%")
                    ->orWhere('unit.title','like',"%{$search}%");
                });
            });

            // ✅ Clone the query before using count()
            $count = (clone $baseQuery)->count();
            $data = $baseQuery->select([
                    'products.*',
                    'category.title as category_name',
                    'unit.title as unit_name',

                    DB::raw("( SELECT SUM(quantity) FROM delivery_note_items 
                    join delivery_notes on delivery_notes.id = delivery_note_items.delivery_note_id  
                    WHERE delivery_note_items.product_id = products.id ) AS dc_out"),

                    DB::raw("(SELECT SUM(qty) FROM stock_adjustment WHERE stock_adjustment.product_id = products.id and stock_adjustment.type = 'out'  ) AS adjustment_out"),

                    DB::raw("(SELECT SUM(qty) FROM stock_adjustment WHERE stock_adjustment.product_id = products.id and stock_adjustment.type = 'in' ) AS adjustment_in")
                ])
                ->orderByDesc('id')
                ->skip($offset)
                ->take($length)
                ->get()
                ->map(function($item){
                    // dd($item);
                    $balance = 0;

                    $balance =  $balance - floatval($item->dc_out);
                    $balance =  $balance - floatval($item->adjustment_out);
                    $balance =  $balance + floatval($item->adjustment_in);

                    $item->balance = $balance;

                    return $item;

            });

            return response()->json([
                'total' => $count,
                'page' => $page,
                
                'offset' => $offset,
                'from' => $count > 0 ? $offset + 1 : 0,
                'to'   =>  $offset + count($data),

                'last_page' => ceil($count / $length),
                'data' => $data,
            ]);
    }




    public function inventoryDetail(Request $request,$id)
    {  

        $model = Product::where('id',$id)->first();
        $balance = 0;

        $baseQuery = ReportService::getInventoryLeder($id);
        $query = DB::query()->fromSub($baseQuery, 'transactions');

        $data = $query->select([
            '*'                    
        ])
        ->orderBy('date')
        ->get()
        ->map(function($item) use(&$balance) {
            $balance = $balance + floatval($item->stock_in);
            $balance = $balance - floatval($item->stock_out);
            $item->balance = floatval($balance);        
            
            $id = $item->unique_id ? explode("_",$item->unique_id) : [];

            $item->id =  isset($id[1]) ? $id[1] : null;

            return $item;
        });


   
        // if ($request->filled('from_date')) {
        //     $data = $data->filter(function ($item) use ($request) {
        //         return date('Y-m-d', strtotime($item->date)) >= $request->from_date;
        //     })->values();
        // }

   
        // if ($request->filled('to_date')) {
        //     $data = $data->filter(function ($item) use ($request) {
        //         return date('Y-m-d', strtotime($item->date)) <= $request->to_date;
        //     })->values();
        // }

        // if ($request->filled('search')) {
        //     $search = strtolower($request->search);
        //     $data = $data->filter(function ($item) use ($search) {
        //         return str_contains(strtolower($item->remarks ?? ''), $search);
        //     })->values();
        // }


        $total = count($data);
        $length = $request->input('length', 20);
        $page   = $request->input('page', 1);
        $offset = ($page - 1) * $length;
        $data = $data->slice($offset, $length)->values();

        return response()->json([
            'total' => $total,
            'page' => $page,
            'offset' => $offset,
            'last_page' => ceil($total / $length),
            'from' => $total > 0 ? $offset + 1 : 0,
            'to'   =>  $offset + count($data),
            'data' => $data,
            'balance' => $balance,
            'prodcut' =>  $model,
        ]);


    }
    




   




  


}


