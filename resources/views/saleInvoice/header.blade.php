<table class="no-border">
    <tr class="" >
        <td width="600px" class="top">
            <table width="100%" class="">
                <tr>
                    <td style="width:90px;" class="top"  >
                        <img style="width:90px" 
                        src="{{ !request()->has('view') 
                                ? public_path('assets/images/invoice-logo.png') 
                                : asset('assets/images/invoice-logo.png') }}" />
                    </td>
                    <td class="center top" >
                        <h1>M. Tariq</h1>
                        <h1 style="font-size: 30px" >Machinery Blocks Works</h1>
                         {{-- <div style="width:500px;font-size:20px;display:block;">
                            Blocks Works
                        </div> --}}
                        {{-- <div style="font-size:14px" >Specialist Machinery Block Manufactured with Stone Concrete</div>
                        <div style="font-size:14px">Plot # KC-972, Street No # 02, Ijtimah Gah Road, Near Quetta Balochistan Hotel, Orangi</div> --}}
                    </td>
                </tr>
            </table>
        </td>
        <td class="center right">
            <div class="bold" style="font-size:14px" >Contact</div>
            <!-- <div style="font-size:13px">M.tariq</div> -->
            <div class="bold" style="font-size:14px">0302-6841570</div>
            <div class="bold" style="font-size:14px">0333-3315283 </div>
            <div class="bold" style="font-size:14px">0309-3315786 </div>
            <!-- <div style="font-size:13px"> 0301-2001477</div> -->
            <!-- <div style="font-size:13px;padding-top: 5px;" >M.arif</div> -->
            
        </td>
    </tr>
     <tr class="" >
        <td colspan="3" class="center" >
            <div class="bold" style="font-size:14px" >Specialist Machinery Block Manufactured with Stone Concrete</div>
            <div class="bold" style="font-size:14px">Plot # KC-972, Street No # 02, Ijtimah Gah Road, Near Quetta Balochistan Hotel, Orangi</div>
        </td>
    </tr>
</table>