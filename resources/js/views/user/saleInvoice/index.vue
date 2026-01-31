<template>
  <v-row>
    <v-col cols="12">
      <v-card>
        <v-card-title class="d-flex align-center justify-space-between">
          <div>
               <div class="text-h6">SaleInvoice</div>
               <div class="text-caption text-grey">View All SaleInvoice Details</div>
          </div>
          <ToolbarOption>
              <v-list-item title="Create" :to="'/user/saleInvoice/create'" />
          </ToolbarOption>
        </v-card-title>
        <v-card-text>
           <v-row class="d-flex flex-row py-3" align="center" no-gutters >
                <v-col cols="auto" class="py-2" >
                    <v-select 
                        label="Length" 
                        v-model="filter.length" 
                        :items="generalStore.sort"  
                        width="150px"
                          />
                </v-col>
                <v-col cols="auto" class="py-2" >
                    <v-text-field 
                        label="Start Date"
                        type="date"
                        clearable 
                        v-model="filter.start_date"   
                        min-width="150px"
                          />
                </v-col>
                <v-col cols="auto" class="py-2" >
                    <v-text-field 
                        label="End Date"
                        type="date"
                        clearable 
                        v-model="filter.end_date"   
                        min-width="150px"
                          />
                </v-col>
                <v-col cols="auto" class="py-2" >
                    <v-text-field 
                        label="Search"
                        clearable
                        v-model="filter.search"   
                        min-width="170px"
                        />
                </v-col>
                <v-col cols="auto" class="py-2" >
                    <UserDropdown 
                      v-model="filter.user_id"
                      clearable
                      min-width="200px"
                      max-width="200px"
                      label="Account" 
                      placeholder="Select Account" />
                </v-col>
                 <v-col cols="auto" class="py-2" >
                    <DeliveryNoteDropdown 
                      :model-value="filter.dc_id"
                      @update:model-value="filter.dc_id = $event"
                      item-title="titleWithRef"
                      item-value="id"
                      clearable
                      min-width="200px"
                      max-width="200px"
                      label="DC" 
                       />
                </v-col>
                <v-col cols="auto" class="py-2" >
                    <v-select 
                      v-model="filter.status"
                      :items="generalStore.status"
                      clearable
                      item-title="title"
                      item-value="value"
                      min-width="200px"
                      max-width="200px"
                      label="Status" 
                      />
                </v-col>
                <v-col cols="auto" class="py-2" >
                    <v-select 
                      v-model="filter.is_paid"
                      :items="generalStore.PaymentStatus"
                      clearable
                      item-title="title"
                      item-value="value"
                      min-width="200px"
                      max-width="200px"
                      label="Payment" 
                      />
                </v-col>
                <v-col cols="auto" class="py-2 px-1">
                  <v-btn class="" color="primary" variant="flat" prepend-icon="mdi-magnify" @click="loadItems">
                  </v-btn>
                </v-col>
                <v-col cols="auto" class="py-2">
                  Showing {{ from }} - {{ to }}   of {{ total}} Records
                </v-col>
            </v-row>
          <v-data-table-server 
            class="border striped-table" 
            :headers="headers" 
            :items="data"   
            :items-length="total"
            :loading="loading"
            item-value="id" 
            >

            <template #item.actions="{ item }">
                <ToolbarOption>
                    <v-list-item title="Edit" :to="'/user/saleInvoice/edit/'+item.id" />
                    <v-list-item target="_blank" title="Print" :href="this.pdfUrl+item.id" />
                    
                    <v-list-item title="Delete" @click="DeleteRecord(item)" />
                </ToolbarOption>
            </template>

            <template #item.user="{ item }">
              {{ item.user?.firstName || '-' }} {{ item.user?.surname || '' }}
            </template>

            <template #item.status="{ item }">
              <v-chip :color="item.status == 1 ? 'green' : 'red'" size="small" dark>
                {{ item.status == 1 ? 'Active' : 'Deactive' }}
              </v-chip>
            </template>

             <template #item.is_paid="{ item }">
              <v-chip :color="item.is_paid == 1 ? 'green' : 'red'" size="small" dark>
                {{ item.is_paid == 1 ? 'Paid' : 'Unpaid' }}
              </v-chip>
            </template>

            <template v-slot:bottom>
              <custom-pagination 
                :loading="loading" 
                v-model:page="filter.page" 
                :lastPage="lastPage"
                @page-changed="loadItems" />
            </template>
          </v-data-table-server>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>

import generaApi from "@/models/general.model"
import UserDropdown from "@/components/UserDropdown.vue"
import ToolbarOption from "@/components/ToolbarOption.vue";
import { useGeneralStore } from "@/stores/generalStore";
import DeliveryNoteDropdown from "@/components/DeliveryNoteDropdown.vue";
import Alert from "@/components/alert.vue";


export default {
  components: {
    UserDropdown,
    ToolbarOption,
    DeliveryNoteDropdown
  },
  data() {
    return {
      generalStore:useGeneralStore(),
      offset:0,
      filter:{
        length:null,
        start_date:null,
        end_date:null,
        user_id:null,
        dc_id:null,
        status:null,
        page:1,
        search:null,
      },
      loading:false,
      data:[],
      total:0,
      lastPage:1,
      from:0,
      to:0,
      headers: [
        { title: "ID", value: "id",sortable: false },
        { title: "Invoice", value: "prefix",sortable: false },
        { title: "Date", value: "date",sortable: false },
        { title: "User", value: "user_name",sortable: false },
        { title: "Status", value: "status",sortable: false },
        { title: "Payment", value: "is_paid",sortable: false },
        { title: "Total", value: "total",sortable: false },
        // { title: "Remarks", value: "remarks",sortable: false },
        { title: "Actions", value: "actions", sortable: false },
      ],
      pdfUrl:`${import.meta.env.VITE_API_BASE_URL}/api/saleInvoice/print/`,
    };
  },
  mounted() {

    this.filter.length = this.generalStore.sort[0];
    this.loadItems();

    // console.log(this.pdfUrl);
    

  },
  methods: {

    async loadItems() {

        this.loading = true;
        try {

            const res = await generaApi.get('/api/saleInvoice',this.filter);
            this.data = res.data;
            this.total = Number(res.total);                
            // this.page = Number(res.current_page);
            this.lastPage = Number(res.last_page);
            this.to = Number(res.to);
            this.from = Number(res.from);
            this.offset = Number(res.offset);
            this.loading = false;  
            // this.length = res.per_page

        } catch (error) {
            this.$alertStore.add(error.message, "error");
            this.data = [];
            this.total = 0;
            this.from = 0;
            this.to = 0;
            this.filter.page = 1;
            this.lastPage = 1;
            this.loading = false;       
        }

    },

    async DeleteRecord(item) {
        if (!confirm("Are you sure you want to delete selected items?")) return ;
            this.loading = true;
            try {
                await generaApi.delete('/api/saleInvoice/'+item.id,{});
                this.$alertStore.add("Selected items deleted successfully","success");
                this.loading = false;
                this.loadItems();
            } catch (error) {
                this.$alertStore.add(error.message, "error");
                this.loading = false;
            }       
          },

     },


};
</script>

<style>
  .toolbar-option{
    cursor: pointer;
  }
</style>