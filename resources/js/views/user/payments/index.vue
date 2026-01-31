<template>
  <v-row>
    <v-col cols="12">
      <v-card title="Receive / payable" subtitle="View All List">
        <v-card-text>

          <v-row class="d-flex flex-row py-3" align="center" no-gutters>
              <v-col cols="auto" class="py-2">
                  <v-select 
                    label="Length" 
                    v-model="filter.length" 
                    :items="generalStore.sort"
                    min-width="100px" />
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
                <UserDropdown 
                  v-model="filter.user_id"
                  clearable
                  min-width="200px"
                  max-width="200px"
                  label="Account" 
                  placeholder="Select Account" />
              </v-col>
              <v-col cols="auto" class="py-2">
                <v-text-field 
                  label="Search" 
                  v-model="filter.search" 
                  max-width="200px"
                  min-width="200px" 
                  clearable
                  persistent-placeholder />
              </v-col>
              <v-col cols="auto" class="py-2 px-1">
                <v-btn class="" color="primary" variant="flat" prepend-icon="mdi-magnify" @click="loadItems">
                  
                </v-btn>
              </v-col>
              <v-col cols="auto" class="py-2 px-1">
                <v-btn class="" color="success" variant="flat" prepend-icon="mdi-plus"
                :to="`/user/payments/create`"></v-btn>
              </v-col>
              <v-col cols="auto" class="py-2">
                Showing {{ filter.offset }}  of {{ totalItems}} Records
              </v-col>
          </v-row>
       

          <v-data-table-server class="border striped-table" :headers="headers" :items="items" :items-length="totalItems"
            :loading="loading" item-value="id" @update:options="loadItems">
            <template #item.image="{ item }">
              <v-img :src="item.image" width="60" height="50" contain></v-img>
            </template>

            <template #item.actions="{ item }">
              <v-btn color="warning" variant="flat" :to="`/user/payments/edit/${item.id}`">
                <v-icon>mdi-square-edit-outline</v-icon>
              </v-btn>
              <span class="px-1"> </span>
              <v-btn color="danger" variant="flat" @click="deleteItem(item.id)">
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </template>

            <template v-slot:bottom>
              <custom-pagination :loading="loading" v-model:page="filter.page" :lastPage="last_page"
                @page-changed="loadItems" />
            </template>
          </v-data-table-server>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>

import UserDropdown from "@/components/UserDropdown.vue";
import generalModel from "@/models/general.model";
import { useGeneralStore } from "@/stores/generalStore";

export default {
  components:{ UserDropdown},
  data() {
    return {
      filter: { 
        search: "", 
        date:null,
        length: null, 
        page: 1, 
        user_id:null,
        offset: 0 
      },
      generalStore:useGeneralStore(),
      items: [],
      totalItems: 0,
      last_page: 1,
      loading: false,
      headers: [
        { title: "ID", value: "id" },
        { title: "Date", value: "date" },
        { title: "Account", value: "firstName" },
        { title: "Remarks", value: "remarks" },
        { title: "Debit", value: "debit" },
        { title: "Credit", value: "credit" },
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
        const res = await generalModel.get('/api/payments', this.filter);
        this.items = res.data;
        this.totalItems = res.total;
        this.last_page = res.last_page;
        this.filter.page = Number(res.page);
        this.filter.offset = res.offset;
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

        const res = await generalModel.delete('/api/payments/' + id, {});
        this.$alertStore.add(res.message || "Inventory deleted", "success");
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
