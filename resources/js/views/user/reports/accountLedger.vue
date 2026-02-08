<template>
  <v-row>
    <v-col cols="12">
      <v-card title="Account Ledger" subtitle="View All Account Details">
        <v-card-text>
         <v-row class="d-flex flex-row py-3" align="center" no-gutters>
            <v-col cols="auto" class="py-2">
                <v-select 
                  label="Length" 
                  v-model="filter.length" 
                  :items="generalStore.sort" 
                  min-width="100px" 
                  max-width="100px"
                />
            </v-col>
            <v-col cols="auto" class="py-2">
              <v-text-field
                class="ml-2"
                label="Search"
                v-model="filter.search"
                min-width="200px"
                max-width="200px"
                clearable
                persistent-placeholder
              />
            </v-col>
            <v-col cols="auto" class="py-2">
                <v-select label="Group" 
                    v-model="filter.group"
                    width="200px"
                    :items="['customer','employe']"
                    persistent-placeholder
                    clearable />
            </v-col>
            <v-col cols="auto" class="py-2 px-1">
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
        
            <template #item.actions="{ item }">
                 <v-btn color="success" variant="plain" :to="`/user/reports/accountLedger/${item.id}`">
                    <v-icon>mdi-eye</v-icon>
                </v-btn>
            </template>
            <template v-slot:bottom>
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
      filter: {
        search: "",
        length: 20,
        page: 1,
        group:null,
      },
      offset:0,
      items: [],
      total: 0,
      last_page: 1,
      loading: false,
      headers: [
        { title: "ID", value: "id", sortable: false },
        { title: "Name", value: "firstName" },
        { title: "Group", value: "group" },
        { title: "Phone", value: "phone" },
        { title: "Address", value: "companyAddress1" },
        
        { title: "Balance", value: "balance" },
        {title: "Actions", value: "actions", sortable: false },
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

          const res = await generalModel.get("/api/reports/customerLedger",this.filter);
          this.items = res.data ?? [];
          this.total = res.total ?? 0;
          this.last_page = res.last_page ?? 1;
          this.filter.page = Number(res.page ?? 1);

          this.to = Number(res.to);
          this.from = Number(res.from);
          this.offset = Number(res.offset);
          

       

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

