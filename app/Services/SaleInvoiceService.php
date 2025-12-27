<?php

namespace App\Services;

use App\Models\Auctions;
use App\Models\DeliveryNote;
use App\Models\Interest;
use App\Models\SaleInvoice;
use App\Models\SaleInvoiceItem;
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
use Mpdf\Tag\P;

class SaleInvoiceService
{

     static public function search(Request $request)
    {   
        
        return SaleInvoice::with([
            'user:id,firstName',
            'items',
            'items.deliveryNote',
        ])
        //Search
        ->when($request->search, function ($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('ref', 'like', "%{$search}%")
                ->orWhereHas('user', fn($q2) => $q2->where('fistName', 'like', "%{$search}%"))
                ->orWhereHas('items.deliveryNote', fn($q2) => $q2->where('ref', 'like', "%{$search}%"));
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

        $model = new SaleInvoice([
            'date'     => Carbon::parse($request->date),
            'due_date'     => Carbon::parse($request->due_date),
            'ref'      => $request->ref,
            'user_id'  => $request->user_id,
            'status'   => $request->status,
            'is_paid'   => $request->is_paid,
            'remarks'  => $request->remarks,
            'subtotal' => 0,
            'discount' => $request->discount ?? 0,
            'tax'      => $request->tax ?? 0,
            'total'    => 0,
        ]);

        $model->save();



        $subtotal = 0;
        foreach ($request->items as $key => $value) {

            $deliver_note = DeliveryNote::find($value['delivery_note_id']);
            $SaleInvoiceItem = SaleInvoiceItem::create([
                'sale_invoice_id' => $model->id,
                "delivery_note_id" => $deliver_note->id,
                "discount" => $value['discount'] ?? 0,
                "tax" => $value['tax'] ?? 0,
                "total" => 0,
            ]);

            $step  = $deliver_note->total;
            $step  = $step - $SaleInvoiceItem->discount;
            $step  = $step + $SaleInvoiceItem->tax;
            $SaleInvoiceItem->total = $step;
            $SaleInvoiceItem->save();

            $subtotal +=  $step;
        }


        $model->subtotal = $subtotal;
        $step = $subtotal - $request->discount;
        $step = $subtotal + $request->tax;

        $model->total = $step;
        $model->save();

        return $model;
    }


    static public function update($id, $request)
    {

        $model = SaleInvoice::where('id', $id)->first();

        $model->update([
            'date'     => Carbon::parse($request->date),
            'due_date'     => Carbon::parse($request->due_date),
            'ref'      => $request->ref,
            'status'   => $request->status,
            'is_paid'   => $request->is_paid,
            'remarks'  => $request->remarks,
            'subtotal' => 0,
            'discount' => $request->discount ?? 0,
            'tax'      => $request->tax ?? 0,
            'total'    => 0,
            'updated_at'     => Carbon::now(),
        ]);


        SaleInvoiceItem::where('sale_invoice_id', $model->id)->delete();

        $subtotal = 0;
        foreach ($request->items as $key => $value) {

            $deliver_note = DeliveryNote::find($value['delivery_note_id']);
            $SaleInvoiceItem = SaleInvoiceItem::create([
                'sale_invoice_id' => $model->id,
                "delivery_note_id" => $deliver_note->id,
                "discount" => $value['discount'] ?? 0,
                "tax" => $value['tax'] ?? 0,
                "total" => 0,
            ]);

            $step  = $deliver_note->total;
            $step  = $step - $SaleInvoiceItem->discount;
            $step  = $step + $SaleInvoiceItem->tax;
            $SaleInvoiceItem->total = $step;
            $SaleInvoiceItem->save();

            $subtotal +=  $step;
        }


        $model->subtotal = $subtotal;
        $step = $subtotal - $request->discount;
        $step = $subtotal + $request->tax;

        $model->total = $step;
        $model->save();

        return $model;
    }



    static public function show($id, $request)
    {

        $model = SaleInvoice::where('id', $id)->first();
        if (!$model) {
          throw new \Exception("Record with ID $id not found");
        }
        return $model;
    }


    static public function delete($id, $request)
    {
        $model = SaleInvoice::where('id', $id)->first();
        if (!$model) {
          throw new \Exception("Record with ID $id not found");
        }
        
        $model->delete();
        SaleInvoiceItem::where('sale_invoice_id', $model->id)->delete();
        return $model;
    }
}
