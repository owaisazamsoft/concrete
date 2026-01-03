<table class="no-border">
    <tr>
        <td width="600px" class="top" style="vertical-align: top">
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
            <!-- <div style="font-size:13px">M.tariq</div> -->
            <div style="font-size:13px">0333-3315283 </div>
            <div style="font-size:13px">0309-3315786 </div>
            <!-- <div style="font-size:13px"> 0301-2001477</div> -->
            <!-- <div style="font-size:13px;padding-top: 5px;" >M.arif</div> -->
            <div style="font-size:13px"> 0302-6841570</div>
        </td>
    </tr>
</table>