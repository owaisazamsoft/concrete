<template>
  <v-row>
    <v-col cols="12">
      <v-card :title="customer?.firstName+' - Ledger'" subtitle="View All Account Details">
        <v-card-text>
          <v-row class="d-flex flex-row py-3" align="center" no-gutters>
            <v-col cols="auto" class="py-2">
              <v-select 
                label="Length"
                max-width="100px" 
                v-model="filter.length" 
                :items="generalStore.sort"  
                width="150"
              />
            </v-col>
            <v-col cols="auto" class="py-2">
              <v-text-field
                class="ml-2"
                label="From Date"
                max-width="200px"
                type="date"
                v-model="filter.from_date"
                min-width="200px"
                clearable
                persistent-placeholder
              />
            </v-col>
            <v-col cols="auto" class="py-2">
              <v-text-field
                class="ml-2"
                label="To Date"
                max-width="200px"
                type="date"
                v-model="filter.to_date"
                min-width="200px"
                clearable
                persistent-placeholder
              />
            </v-col>
            <v-col cols="auto" class="py-2">
              <v-text-field
                class="ml-2"
                label="Search"
                max-width="200px"
                v-model="filter.search"
                width="200"
                clearable
                persistent-placeholder
              />
            </v-col>
            <v-col cols="auto" class="py-2">
              <v-btn class="ml-2" color="primary" variant="flat" prepend-icon="mdi-magnify" @click="loadItems"></v-btn>
            </v-col>
            <v-col cols="auto" class="py-2">
                  Showing {{ from }} - {{ to }}   of {{ total}} Records
            </v-col>
          </v-row>
        

          <v-data-table-server class="border striped-table"
            :headers="headers"
            :items="items"
            :items-length="total"
            :loading="loading"
            item-value="id"
          >
            <template #item.image="{ item }">
              <v-img :src="item.image" width="60" height="50" contain></v-img>
            </template>

            <template #item.action="{ item }">
                 <v-btn v-if="item.type == 'sale'" color="success" variant="plain" 
                  :to="`/user/deliverynote/edit/${item.id}`">
                    <v-icon>mdi-eye</v-icon>
                </v-btn>
                 <v-btn v-else-if="item.type == 'payment'" color="success" variant="plain" :to="`/user/payments/edit/${item.id}`">
                    <v-icon>mdi-eye</v-icon>
                </v-btn>
            </template>


            <template v-slot:bottom>
              <div class="border-t border-b py-3 d-flex justify-center">
                <div style="width: 200px;" class="text-end font-weight-bold" >Current Balance :</div>
                <div style="width: 100px;" class="px-3"  >{{ balance }}</div>
              </div>
  
              <custom-pagination
                :loading="loading"
                v-model:page="filter.page"
                :lastPage="last_page"
                @page-changed="loadItems"
              />
            </template>

          </v-data-table-server>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import generalModel from "@/models/general.model";
import { useGeneralStore } from "@/stores/generalStore";


export default {
  data() {
    return {
      generalStore:useGeneralStore(),
      customer:{},
      balance:0,
      filter: {
        search: "",
        length: 10,
        page: 1,
      },
      offset: 0,
      to:0,
      from:0,
      items: [],
      total: 0,
      last_page: 1,
      loading: false,
      headers: [
        { title: "ID", value: "id", sortable: false },
        { title: "Date", value: "date" },
        { title: "Description", value: "remarks" },
        { title: "Debit", value: "debit" },
        { title: "Credit", value: "credit" },
        { title: "Balance", value: "balance" },
        { title: "Action", value: "action" },
      ],
    };
  },

  mounted() {
    this.filter.length = this.generalStore.sort[0];
    this.loadItems();
  },

  methods: {
    async loadItems() {
      this.loading = true;

      try {

      
        const id = this.$route.params.id;
        const res = await generalModel.get("/api/reports/customerLedgerDetail/"+id,this.filter);

        this.items = res.data ?? [];
        this.total = res.total ?? 0;
        this.last_page = res.last_page ?? 1;
        this.filter.page = Number(res.page ?? 1);
        this.offset = Number(res.offset);
        this.from = Number(res.offset);
        this.to = Number(res.to);

        this.balance = res.balance;
        this.customer = res.customer;

      } catch (error) {
        console.error(error);
        this.items = [];
        this.total = 0;
      } finally {
        this.loading = false;
      }


    },
  },
};
</script>

