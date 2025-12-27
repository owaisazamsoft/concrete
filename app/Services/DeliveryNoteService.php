<?php

namespace App\Services;

use App\Models\Auctions;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\Interest;
use App\Models\SaleInvoiceItem;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class DeliveryNoteService 
{

     static public function search(Request $request)
    {   
        
        return DeliveryNote::with([
            'user',
            'items',
            'items.product',
            'saleOrder',
            'saleInvoiceItem'
            
        ])
        //Search
        ->when($request->search, function ($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('ref', 'like', "%{$search}%")
                ->orWhereHas('user', fn($q2) => $q2->where('fistName', 'like', "%{$search}%"))
                ->orWhereHas('items.product', fn($q2) => $q2->where('title', 'like', "%{$search}%"));
            });
        })
        // User Id
        ->when($request->user_id, function ($query, $value) {
            $query->where('user_id',$value);
        })
        //Start Date
        ->when($request->start_date, function ($query, $value) {
            $query->whereDate('date', '>=', $value);
        })
        // End Date
        ->when($request->end_date, function ($query, $value) {
            $query->whereDate('date', '<=', $value);
        })
        ->orderByDesc('date')
        ->paginate($request->length ?? 10);

    }
    
      static public function create($request)
    {   

        $saleOrder = SaleOrderService::create($request);

        $order = DeliveryNote::create([
            'sale_order_id' => $saleOrder->id,
            'user_id'  => $request->user_id,
            'date'     => Carbon::parse($request->date),
            'ref'      => $request->ref,
            'status'   => $request->status,
            'remarks'  => $request->remarks,
            'total'    => 0,
        ]);

        $subtotal = 0;
        foreach ($request->items as $key => $value) {
           
            $orderItem = new DeliveryNoteItem([
                "delivery_note_id" => $order->id,
                "product_id" => $value['product_id'],
                "quantity" => $value['quantity'],
                "price" => $value['price'],
            ]);

            $step  = $orderItem->quantity * $orderItem->price;
            $orderItem->total = $step;
            $orderItem->save();
            $subtotal +=  $step;

        }

        $order->total = $subtotal;
        $order->save();

        return $order;

    }


      static public function update($id,$request)
    {   

 
        $order = DeliveryNote::where('id',$id)->first();
        if (!$order) {
          throw new \Exception("Record with ID $id not found");
        }
    
        SaleOrderService::Update($order->sale_order_id,$request);

        $order->update([
            'date'     => Carbon::parse($request->date),
            'ref'      => $request->ref,
            'status'   => $request->status,
            'remarks'  => $request->remarks,
            'total'    => 0,
        ]);

        $subtotal = 0;
      
        DeliveryNoteItem::where('delivery_note_id',$order->id)->delete();
        foreach ($request->items as $key => $value) {
           
            $orderItem = new DeliveryNoteItem([
                "delivery_note_id" => $order->id,
                "product_id" => $value['product_id'],
                "quantity" => $value['quantity'],
                "price" => $value['price'],
            ]);

            $step  = $orderItem->quantity * $orderItem->price;
            $orderItem->total = $step;
            $orderItem->save();
            $subtotal +=  $step;

        }

        $order->total = $subtotal;
        $order->save();

        return $order;

    }

        static public function show($id,$request)
    {   

        $model = DeliveryNote::where('id',$id)->first();
        if (!$model) {
          throw new \Exception("Record with ID $id not found");
        }

        return $model;

    }


        static public function delete($id,$request)
    {   

        $model = DeliveryNote::where('id',$id)->first();
        if (!$model) {
          throw new \Exception("Record with ID $id not found");
        }

        if(SaleInvoiceItem::where('delivery_note_id',$id)->first()){
            throw new \Exception("Cannot Deleted Record Its Used In Delivery Note");
        }

        DeliveryNoteItem::where('delivery_note_id',$id)->delete();


        $model->delete();
        SaleOrder::where('id', $model->sale_order_id)->delete();

        return $model;

    }




  
}
