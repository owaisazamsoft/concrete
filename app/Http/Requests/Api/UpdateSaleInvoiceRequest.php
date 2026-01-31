<?php

namespace App\Http\Requests\Api;

use App\Models\SaleInvoiceItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateSaleInvoiceRequest extends FormRequest
{
    public function rules()
    {
        
        $routeParam = $this->route('saleInvoice');
        $invoiceId = is_object($routeParam) ? $routeParam->id : $routeParam;
       

        return [
            // 'user_id' =>['required','integer',Rule::exists('users','id')],
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'ref' => 'nullable|string|max:1000',
            'status' => 'required|in:0,1',
            'is_paid' => 'required|in:0,1',

            'items' => 'required|array|min:1',
            'items.*.delivery_note_id' => [
                'required',
                'integer',
                'exists:delivery_notes,id',
                function ($attribute, $value, $fail) use ($invoiceId) {
                    
                  $invoice = SaleInvoiceItem::select([
                    'sale_invoices.prefix',
                    'delivery_notes.ref',
                   ])->where('delivery_note_id',$value)
                  ->join('delivery_notes', 'delivery_notes.id', '=', 'sale_invoice_items.delivery_note_id')
                  ->join('sale_invoices', 'sale_invoices.id', '=', 'sale_invoice_items.sale_invoice_id') 
                  ->whereNot('sale_invoices.id',$invoiceId)
                  ->first();
                    if ($invoice) {
                        $fail("The selected Delivery Note (ID: {$invoice->ref}) is already linked to another invoice. {$invoice->prefix}");
                    }

                },
            ],
            
            
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',

            'discount' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'remarks' => 'nullable|string|max:1000',           

        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {

        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422)
        );

    }

}
