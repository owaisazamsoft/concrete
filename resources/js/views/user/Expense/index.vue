<template>
  <v-row>
    <v-col cols="12">
      <v-card title="Expense" subtitle="View All Expense Details">
        <v-card-text>
          <v-row class="d-flex flex-row" no-gutters="" align="center" >
              <v-col cols="auto" class="py-2">
                  <v-select 
                  label="Length" 
                  v-model="filter.length" 
                  :items="generalStore.sort"  
                  width="150px"
                  />
               </v-col>
               <v-col cols="auto" class="py-2">
                  <v-text-field 
                    label="Date" 
                    v-model="filter.date" 
                    max-width="200px" 
                    type="date"
                    clearable
                    persistent-placeholder />
               </v-col>
               <v-col cols="auto" class="py-2">
                  <ExpenseCategory
                    min-width="150px" 
                    v-model="filter.category_id"
                    clearable  
                    placeholder="Category" />
               </v-col>
               <v-col cols="auto" class="py-2">
                  <v-text-field
                    label="Search"
                    v-model="filter.search"
                    min-width="200px"
                    clearable
                    persistent-placeholder
                  />
               </v-col>
               <v-col cols="auto" class="py-2">
                  <v-btn class="ml-2" color="primary" variant="flat" prepend-icon="mdi-magnify" @click="loadItems"></v-btn>
               </v-col>
               <v-col cols="auto" class="py-2">
                  <v-btn class="ml-2" color="success" variant="flat" prepend-icon="mdi-plus" :to="`/user/expense/create`"></v-btn>
               </v-col>
               <v-col cols="auto" class="py-2">
                  Showing {{ from }} - {{ to }}   of {{ totalItems}} Records
                </v-col>
          </v-row>

          <v-data-table-server class="border striped-table"
            :headers="headers"
            :items="items"
            :items-length="totalItems"
            :loading="loading"
            item-value="id"
         
          >
         
            <template #item.actions="{ item }">
                 <v-btn color="warning" variant="flat" :to="`/user/expense/edit/${item.id}`">
                    <v-icon>mdi-square-edit-outline</v-icon>
                </v-btn>
            <span class="px-1"> </span>
            <v-btn
                color="danger"
                variant="flat"
                @click="deleteItem(item.id)"
                >
                <v-icon>mdi-delete</v-icon> 
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

import ExpenseCategory from "@/components/ExpenseCategory.vue";
import generalModel from "@/models/general.model";
import { useGeneralStore } from "@/stores/generalStore";

export default {
  components:{ExpenseCategory},
  data() {
    return {
      generalStore:useGeneralStore(),
      filter: { 
        search: "", 
        length: null, 
        page: 1, 
        date:null,
        category_id:null 
      },
      items: [],
      totalItems: 0,
      offset:0,
      from:0,
      to:0,
      last_page: 1,
      loading: false,
      headers: [
        { title: "ID", value: "id",sortable: false },
        { title: "Date", value: "date",sortable: false },
        { title: "Category", value: "category.title",sortable: false },
        { title: "Debit", value: "debit",sortable: false },
        { title: "Credit", value: "credit",sortable: false },
        { title: "Remarks", value: "remarks",sortable: false },
        { title: "Actions", value: "actions", sortable: false },
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
        
          const res = await generalModel.get('/api/expenses',this.filter);
          this.items = res.data;
          this.totalItems = res.total;
          this.last_page = res.last_page;
          this.filter.page = Number(res.page);

          this.offset = Number(res.offset);
          this.from = Number(res.from);
          this.to = Number(res.to);

      } catch (error) {
          this.items = [];
          this.totalItems = 0;

          this.offset = 0;
          this.from = 0;
          this.to = 0;
      } finally {
          this.loading = false;
      }

    },
    async deleteItem(id) {
        
        if (!confirm("Are you sure you want to delete this item?")) return;

        this.loading = true;
        try {
          const res = await generalModel.delete('/api/expenses/'+id,{});
          this.$alertStore.add(res.message, "success");
          this.loadItems(); 

        } catch (error) {
          console.error(error);
          this.$alertStore.add(error.message, "error");
        } finally {
          this.loading = false;
        }

    }

  },
};
</script>
