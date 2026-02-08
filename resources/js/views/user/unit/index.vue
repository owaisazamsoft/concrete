<template>
  <v-row>
    <v-col cols="12">
      <v-card title="Unit" subtitle="View All Unit Details">
        <v-card-text>
          <v-row class="d-flex flex-row py-3" align="center" no-gutters="" >
            <v-col cols="auto" class="py-2" >
              <v-select 
                max-width="100px"
                label="Length" 
                v-model="filter.length" 
                :items="generalStore.sort"  
                width="150px"
              />
            </v-col>
            <v-col cols="auto" class="py-2">
              <v-text-field
                max-width="200px"
                label="Search"
                v-model="filter.search"
                min-width="150px"
                clearable
                persistent-placeholder
              />
            </v-col>
            <v-col cols="auto" class="py-2 px-1" >
              <v-btn 
                class="ml-2" 
                color="primary" 
                variant="flat" 
                prepend-icon="mdi-magnify" 
                @click="loadItems"></v-btn>
            </v-col>
            <v-col cols="auto" class="py-2 px-1" >
              <v-btn 
                 color="success" 
                 variant="flat" 
                 prepend-icon="mdi-plus" 
                 :to="`/user/unit/create`"></v-btn>
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
            <template #item.image="{ item }">
              <v-img :src="item.image" width="60" height="50" contain></v-img>
            </template>

            <template #item.actions="{ item }">
                 <v-btn color="warning" variant="flat" :to="`/user/unit/edit/${item.id}`">
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
import unitModel from "@/models/unit.model";
import { useGeneralStore } from "@/stores/generalStore";

export default {
  data() {
    return {
      generalStore:useGeneralStore(),
      filter: { 
        search: "", 
        length: null, 
        page: 1, 
      },
      items: [],
      totalItems: 0,
      last_page: 1,
      loading: false,
      offset:0,
      from:0,
      to:0,
      headers: [
        { title: "ID", value: "id",sortable: false },
        { title: "Title", value: "title" },
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
        const res = await unitModel.all(this.filter);
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
      } finally {
        this.loading = false;
      }
    },
    async deleteItem(id) {
        if (!confirm("Are you sure you want to delete this item?")) return;

        this.loading = true;
        try {
        const res = await unitModel.delete(id);

        this.$alertStore.add(res.message || "Inventory deleted", "success");
        this.loadItems(); 

        } catch (error) {
        console.error(error);
        this.$alertStore.add(error.message || "Delete failed", "error");
        } finally {
        this.loading = false;
        }
    }

  },
};
</script>
