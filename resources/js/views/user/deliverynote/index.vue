<template>
  <v-row>
    <v-col cols="12">
      <v-card title="Delivery Notes" subtitle="View All Delivery Notes Details">
        <v-card class="" outlined>
          <v-card-text>

            <v-row dense>
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="filter.search"
                  label="Search"
                  prepend-inner-icon="mdi-magnify"
                  clearable
                  density="compact"
                  persistent-placeholder=""
                />
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="filter.date"
                  label="Start Date"
                  type="date"
                  density="compact"
                />
              </v-col>
         
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="filter.date"
                  label="End Date"
                  type="date"
                  density="compact"
                />
              </v-col>
             </v-row>
              <v-row dense class="mt-2">
                <v-col cols="3" sm="4">
                    <UserDropdown
                        v-model="filter.user_id"
                        label="Users"
                        clearable persistent-placeholder=""
                    />
                </v-col>
                <v-col cols="12" sm="4">
                  <v-select
                    v-model="filter.status"
                    :items="statusItems"
                    item-title="label"
                    item-value="value"
                    label="Status"
                    clearable persistent-placeholder=""
                  />

                </v-col>
              </v-row>
           
              <v-col cols="12" class="text-center mt-3">
                <v-btn
                  color="primary"
                  prepend-icon="mdi-filter"
                  class="mr-2"
                  @click="loadItems"
                >
                  Search
                </v-btn>
                <v-btn 
                class="ml-2" 
                color="success" 
                variant="flat" 
                prepend-icon="mdi-plus" 
                :to="`/user/deliverynote/create`"></v-btn>
           
              </v-col>
          </v-card-text>
        </v-card>
        <v-card-text>
          <v-row class="mb-2">
            <v-col cols="12" class="d-flex align-center flex-wrap">
              <v-select
                v-model="filter.length"
                :items="[10, 20, 50, 100]"
                density="compact"
                variant="outlined"
                max-width="120"
                class="mr-3"
                label="Per Page"
                @change="loadItems"
              />
              <div class="align-self-center">
                Showing {{ filter.offset + 1 }} - {{ Math.min(filter.offset + items.length, total) }} of {{ total }} Records
              </div>

              <v-spacer />
            </v-col>
          </v-row>


          <v-data-table-server
            :headers="headers"
            :items="items"
            :items-length="total"
            :loading="loading"
            v-model:page="filter.page"
            :items-per-page="filter.length"
            :items-per-page-options="[10, 20, 50, 100]"
       
            @update:options="loadItems"
          >
 
            <template #item.actions="{ item }">
              <v-btn color="warning" variant="flat" :to="`/user/deliverynote/edit/${item.id}`">
                <v-icon>mdi-square-edit-outline</v-icon>
              </v-btn>
              <v-btn color="danger" variant="flat" class="ml-1" @click="deleteItem(item.id)">
                <v-icon>mdi-delete</v-icon>
              </v-btn>
            </template>

 
            <template #item.user="{ item }">
              {{ item.user?.firstName || '-' }} {{ item.user?.surname || '' }}
            </template>

     
            <template #item.status="{ item }">
              <v-chip :color="item.status == 1 ? 'green' : 'red'" size="small" dark>
                {{ item.status == 1 ? 'Active' : 'Deactive' }}
              </v-chip>
            </template>

            <template #item.sale_order="{ item }">
              {{ item.sale_order.ref}}
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
import generaApi from "@/models/general.model"
import UserDropdown from "@/components/UserDropdown.vue"
export default {
  components: {
    UserDropdown
  },
  data() {
    return {
      
      filter: {   search: "", 
        length: 10, 
        page: 1, 
        offset: 0, 
        start_date: "", 
        end_date: "", 
        user_id: null, 
        status: null  },
      items: [],
      total: 0,
      last_page: 1,
      loading: false,
      headers: [
        { title: "ID", value: "id" },
        { 
          title: "Date", 
          value: "date",
          format: (value) => value ? value.split(' ')[0] : ''
        },
        { title: "User", value: "user" },
        { title: "Sale Order", value: "sale_order" },
        { title: "Sale Invoice", value: "sale_invoice_item" },
        { title: "Ref", value: "ref" },
        { title: "Remarks", value: "remarks" },
        { title: "Status", value: "status" },
        { title: "Total", value: "total" },
        { title: "Actions", value: "actions", sortable: false },
      ],
      url :"/api/deliveryNotes/",
      statusItems: [
        { label: "Active ", value: 1 },
        { label: "Deactive", value: 0 },
        ],
    };
  },
  mounted() {
    this.loadItems();
  },
  methods: {
   
  async loadItems(options = {}) {
    this.loading = true;

    try {
      if (options.page) this.filter.page = options.page;
      if (options.itemsPerPage) this.filter.length = options.itemsPerPage;

      const params = {
        page: this.filter.page,
        per_page: this.filter.length,
        search: this.filter.search,
        start_date: this.filter.start_date,
        end_date: this.filter.end_date,
        user_id: this.filter.user_id,
        status: this.filter.status,
      };

      const res = await generaApi.getAndfind(this.url, params);

      this.items = res.data;         
      this.total = res.total;   
      this.last_page = res.last_page;
      this.filter.page = res.current_page;
      this.filter.offset = res.from - 1 || 0;
    } catch (error) {
      this.items = [];
      this.total = 0;
      this.last_page = 1;
    } finally {
      this.loading = false;
    }
  },

    async deleteItem(id) {
        if (!confirm("Are you sure you want to delete this item?")) return;

        this.loading = true;
        try {
        const deleteurl = this.url+id
        const res = await generaApi.delete(deleteurl);

        this.$alertStore.add(res.message || "Sale Invoice deleted", "success");
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
