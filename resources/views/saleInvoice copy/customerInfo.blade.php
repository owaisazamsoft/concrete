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