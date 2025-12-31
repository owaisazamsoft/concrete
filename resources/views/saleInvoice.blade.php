<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { 
        font-family: Arial, Helvetica, sans-serif; 
        font-size: 11px; 
    }
    
    h1 { font-size: 22px; margin: 0; }
    table { width: 100%; border-collapse: collapse; }

    td, th { border: 1px solid lightgray; padding: 4px; color: inherit; }
    
    .no-border td { border: none; }
    
    .right { text-align: right; }
    
    .center { text-align: center; }

    .left{
        text-align: left;
    }
    
    .bold { font-weight: bold; }
    
    .gray { 
        background: green;
    }

    .white{
        color: white;
    }
    
    th{
        font-weight: bold;
    }

    .customer-table b{
        padding-bottom: 2px;
    }

    .subheading{  
        background: #008000;
        color: white;
        padding: 3px 5px;
        font-size: 13px;
        font-weight: bold;
    }

    .border{
        border:1px solid black!important;
    }

    .top{
        vertical-align: top;
    }

    .w-100{
        width: 100%;
    }

    .block{
        display: block;
    }

</style>
</head>
<body>

<table class="no-border">
    <tr>
        <td width="500px" class="top" style="vertical-align: top">
            <table width="100%" class="">
                <tr>
                    <td style="width: 100px;" >
                        <img style="width:100px" 
                        src="{{ !request()->has('view') 
                                ? public_path('assets/images/invoice-logo.png') 
                                : asset('assets/images/invoice-logo.png') }}" />
                    </td>
                    <td>
                        <h1 >M. Tariq Machinery Blocks Works</h1>
                        <div style="font-size:13px" >Specialist Machinery Block Manufactured with Stone Concrete</div>
                        <div style="font-size:13px">Plot # KC-972, Street No # 02, Ijtimah Gah Road, Near Quetta Balochistan Hotel, Orangi</div>
                    </td>
                </tr>
            </table>
        </td>
        <td class="top right">
            <div style="font-size:13px" >Contact</div>
            <div style="font-size:13px">M.tariq</div>
            <div style="font-size:13px">0333-3315283 </div>
            <div style="font-size:13px"> 0301-2001477</div>
            <div style="font-size:13px;padding-top: 5px;" >M.arif</div>
            <div style="font-size:13px"> 0302-6841570</div>
        </td>
    </tr>
</table>

<br>

<table class="customer-table no-border"  >
    <tr>
    <td  width="50%">
        <table class="no-border" >
            <tr>
                <td colspan="3" class="subheading" >
                    Bill To
                </td>
            </tr>
            <tr>
                <td style="width: 50px;"><b>Name</b></td>
                <td style="width: 10px;" >:</td>
                <td class="left" >{{ $data->user->firstName }}</td>
            </tr>
            <tr>
                <td style="width: 50px;"><b>NTN</b></td>
                <td style="width: 10px;">:</td>
                <td class="left" >{{ $data->user->ntn }}</td>
            </tr>
            <tr>
                <td style="width: 50px;"><b>Address</b></td>
                <td style="width: 10px;">:</td>
                <td class="left" >{{ $data->user->companyAddress1 }}</td>
            </tr>
            <tr>
                <td style="width: 50px;" ><b>Remarks</b></td>
                <td style="width: 10px;">:</td>
                <td class="left" > </td>
            </tr>
        </table>
    </td>
    <td width="20%" >

    </td>
    <td width="30%" class="top" >
         <table class="no-border" >
            <tr>
                <td colspan="3" class="subheading">Invoice</td>
            </tr>
            <tr>
                <td style="width: 90px;"><b>Invoice No</b></td>
                <td style="width: 10px;">:</td>
                <td>{{ $data->prefix}}</td>
            </tr>
            <tr>
                <td style="width: 90px;" ><b>Invoice Date</b></td>
                <td style="width: 10px;">:</td>
                <td>{{ date('d-M-Y',strtotime($data->date)) }} </td>
            </tr>
            
            <!-- <tr>
                <td style="width: 90px;"><b>P O No</b></td>
                <td style="width: 10px;">:</td>
                <td></td>
            </tr> -->
        </table>  
      </td>
    </tr>
</table>

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
                    <td rowspan="{{$item->deliveryNote->items}}" class="center" >{{ $i+1 }}</td>

                    <td rowspan="{{$item->deliveryNote->items}}" class="center" >
                    {{ date('d-M-Y',strtotime($item->deliveryNote->date)) }} 
                    </td>

                    <td rowspan="{{$item->deliveryNote->items}}" class="center" >
                    
                    {{ $item->deliveryNote->ref }}</td>
                @endif
               
                <td class="" >{{ $dc->product->title }} ({{ $dc->product->unit->title }})</td>
                <td class="center">{{ number_format($dc->quantity,2) }}</td>
                <td class="center">{{ number_format($dc->price,2) }}</td>
           
                <td class="center">{{ number_format($dc->total,2) }}</td>
            </tr>
        @endforeach

    @endforeach

    <!-- <tr class="bold">
        <td colspan="4" class="right">Item Total =&gt;&gt;</td>
        <td class="center">0</td>
        <td></td>
        <td class="right">0</td>
    </tr>
    <tr class="bold">
        <td colspan="4" class="right">Gross Total =&gt;&gt;</td>
        <td class="center">0</td>
        <td></td>
        <td class="right">0</td>
    </tr> -->
    <tr class="bold">
        <td style="border-bottom: 0px;border-left:0px" colspan="5" > 

     
        
        </td>
        <th class="center">Total</th>
        <td class="center">{{ number_format($data->total,2) }}</td>
    </tr>

</table>

<br>

<b>Amount In Words:</b> {{ ucwords(trim((new NumberFormatter('en', NumberFormatter::SPELLOUT))->format($data->total))) }} rupees

</body>
</html>


