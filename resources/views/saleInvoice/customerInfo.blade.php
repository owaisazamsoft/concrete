 <table class="customer-table no-border"  >
    <tr>
        <td  width="50%">
            <table class="no-border" >
                <tr>
                    <td colspan="3" class="label subheading text" >
                        Bill To
                    </td>
                </tr>
                <tr>
                    <td class="text" style="width: 50px;"><b>Name</b></td>
                    <td style="width: 10px;" >:</td>
                    <td class="left text" >{{ $data->user->firstName }}</td>
                </tr>
                <tr>
                    <td class="text" style="width: 50px;"><b>NTN</b></td>
                    <td style="width: 10px;">:</td>
                    <td class="left text" >{{ $data->user->ntn }}</td>
                </tr>
                <tr>
                    <td class="text" style="width: 50px;"><b>Address</b></td>
                    <td style="width: 10px;">:</td>
                    <td class="text left" >{{ $data->user->companyAddress1 }}</td>
                </tr>
                <tr>
                    <td class="text" style="width: 50px;" ><b>Remarks</b></td>
                    <td style="width: 10px;">:</td>
                    <td class="text" class="left" > </td>
                </tr>
            </table>
        </td>
        <td width="20%" >

        </td>
        <td width="30%" class="top" >
            <table class="no-border" >
                <tr>
                    <td colspan="3" class="label subheading text">Invoice</td>
                </tr>
                <tr>
                    <td class="text" style="width: 90px;"><b>Invoice No</b></td>
                    <td style="width: 10px;">:</td>
                    <td class="text" >{{ $data->prefix}}</td>
                </tr>
                <tr>
                    <td class="text" style="width: 90px;" ><b>Invoice Date</b></td>
                    <td style="width: 10px;">:</td>
                    <td class="text" >{{ date('d-M-Y',strtotime($data->date)) }} </td>
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