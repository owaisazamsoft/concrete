<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

    @include('saleInvoice.style')

</head>
<body>

            @include('saleInvoice.header')
            <br>
            @include('saleInvoice.customerInfo')
                   <br>
            <table>
                <tr class="gray bold">
                    <th class="center  white" >#</th>
                    <th class="center white" >Date</th>
                    <th class="center white">DC</th>
                    <th class="center white">Item</th>
                    <th class="center white">Qty</th>
                    <th class="center white">Price</th>
                    <th class="center white">Net Total</th>
                </tr>
  
    
                @foreach($data->items as $i => $item)

                    @foreach($item->deliveryNote->items as $key => $dc)

                        <tr>
                            @if($key == 0)
                                <td rowspan="{{count($item->deliveryNote->items)}}" class="center" >{{ $i+1 }}</td>

                                <td rowspan="{{count($item->deliveryNote->items)}}" class="center" >
                                {{ date('d-M-Y',strtotime($item->deliveryNote->date)) }} 
                                </td>

                                <td rowspan="{{count($item->deliveryNote->items)}}" class="center" >
                                
                                {{ $item->deliveryNote->ref }}</td>
                            @endif
                        
                            <td class="" >{{ $dc->product->title }} ({{ $dc->product->unit->title }})</td>
                            <td class="center">{{ number_format($dc->quantity,2) }}</td>
                            <td class="center">{{ number_format($dc->price,2) }}</td>
                    
                            <td class="center">{{ number_format($dc->total,2) }}</td>
                        </tr>
                    @endforeach

                @endforeach

            <tr class="bold">
                <td style="border-bottom: 0px;border-left:0px" colspan="5"></td>
                <th class="center">Total</th>
                <td class="center">{{ number_format($data->total,2) }}</td>
            </tr>

        </table>

        <br>

        <b>Amount In Words:</b> {{ ucwords(trim((new NumberFormatter('en', NumberFormatter::SPELLOUT))->format($data->total))) }} rupees




 

</body>
</html>


