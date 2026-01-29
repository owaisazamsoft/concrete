<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

    @include('saleInvoice.style')
    <?php  $s = 0; ?>
</head>
<body>
        @include('saleInvoice.header')
        <br>
        @include('saleInvoice.customerInfo')
        <br>
        <table>
            <tr class="gray bold">
                <th class="center white label" >#</th>
                <th class="center white label" >Date</th>
                <th class="center white label">DC</th>
                <th class="center white label">Item</th>
                <th class="center white label">Qty</th>
                <th class="center white label">Price</th>
                <th class="center white label">Net Total</th>
            </tr>

            @foreach($data->items->sortBy('id') as $i => $item)

                @foreach($item->deliveryNote->items as $key => $dc)
                <?php $s += 1; ?>

                    <tr>
                        @if($key == 0)
                            <td rowspan="{{count($item->deliveryNote->items)}}" class="center text" >
                                {{ $s }}</td>
                            <td rowspan="{{count($item->deliveryNote->items)}}" class="center text" >
                            {{ date('d-M-Y',strtotime($item->deliveryNote->date)) }} 
                            </td>
                            <td rowspan="{{count($item->deliveryNote->items)}}" class="center text" >
                            {{ $item->deliveryNote->ref }}</td>
                        @endif
                        <td class="text" >{{ $dc->product->title }}</td>
                        <td class="center text">{{ number_format($dc->quantity,2) }}</td>
                        <td class="center text">{{ number_format($dc->price,2) }}</td>
                        <td class="center text">{{ number_format($dc->total,2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        <tr class="bold">
            <td style="border-bottom: 0px;border-left:0px" colspan="5"></td>
            <th style="background-color: lightgray;color:black;" class="center text">Total</th>
            <td class="center text" style="background:lightgray" ><b>{{ number_format($data->total,2) }} </b></td>
        </tr>

            <!-- <tr class="bold">
            <td style="border-top: 0px;border-bottom: 0px;border-left:0px" colspan="5"></td>
            <th style="background-color: lightgray;color:black;" class="center">Prev Balance</th>
            <td class="center" style="background:lightgray" ><b> </b></td>
        </tr> -->

            <!-- <tr class="bold">
            <td style="border-top: 0px;border-bottom: 0px;border-left:0px" colspan="5"></td>
            <th style="background-color: lightgray;color:black;" class="center">Net Total</th>
            <td class="center" style="background:lightgray" ><b> </b></td>
        </tr> -->
    </table>

    <br>
    <div style="font-size: 13px;" >
        <b>Amount In Words: {{ ucwords(trim((new NumberFormatter('en', NumberFormatter::SPELLOUT))->format($data->total))) }} rupees</b> 
    </div>

    


        <table width="100%" style="" class="" >
                <tr>
                    <td colspan="3" 
                        style="border:0" height="40px" ></td>
                </tr>
                <tr>
                    <td  
                        style="
                        border:0;
                        padding:5px;
                        font-size:13px;
                        font-weight:bold">
                        Customer Signature :
                    </td>
                    <td width="20%"
                        style="border:0;border-bottom:1px solid black;" >
                    </td>
                    <td width="15%" style="border:none" ></td>
                    <td 
                        style="
                        border:0;
                        font-size:13px;
                        font-weight:bold;
                        padding:6px; ">
                        Prepared By :
                    </td>
                    <td width="20%" style="border:0;border-bottom:1px solid black; ">
                    
                    </td>
                </tr>
        </table>
</body>
</html>


