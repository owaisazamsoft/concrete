<template>
  <v-row>
    <v-col cols="12">
      <v-card>
        <v-card-title class="d-flex align-center justify-space-between">
          <div>
            <div class="text-h6">Sale Order</div>
            <div class="text-caption text-grey">
              View All Sale Order Details
            </div>
          </div>
          <v-menu location="bottom end">
            <template #activator="{ props }">
              <v-btn
                icon="mdi-dots-vertical"
                variant="text"
                v-bind="props"
              />
            </template>

              <v-list>
                <v-list-item :to="`/user/saleorder/create`">
                  <v-list-item-title class="d-flex align-center">
                    <v-icon size="18" class="me-2">mdi-plus</v-icon>
                    Create
                  </v-list-item-title>
                </v-list-item>

                <v-list-item @click="reloadPage">
                  <v-list-item-title class="d-flex align-center text-primary">
                    <v-icon size="18" class="me-2" color="blue">mdi-reload</v-icon>
                    Reload
                  </v-list-item-title>
                </v-list-item>

                <v-list-item @click="deleteSelected">
                  <v-list-item-title class="d-flex align-center text-red">
                    <v-icon size="18" class="me-2" color="red">mdi-delete</v-icon>
                    Delete Selected
                  </v-list-item-title>
                </v-list-item>
        
              </v-list>

          </v-menu>
        </v-card-title>




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
                <v-col cols="3" sm="4">
                  <v-select
                    v-model="filter.status"
                    :items="statusItems"
                    item-title="label"
                    item-value="value"
                    label="Status"
                    clearable persistent-placeholder=""
                  />
                </v-col>
                <v-col cols="3" sm="4">
                  <v-select
                    v-model="filter.length"
                    :items="[10, 20, 50, 100]"
                    density="compact"
                    variant="outlined"
                   
                    label="Per Page"
                    @change="loadItems"
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
           
              </v-col>
          </v-card-text>
        </v-card>
        <v-card-text>
          <v-row class="mb-2">
            <v-col cols="12" class="d-flex align-center flex-wrap">

              <div class="align-self-center">
                Showing {{ filter.offset + 1 }} - {{ Math.min(filter.offset + items.length, total) }} of {{ total }} Records
              </div>

              <v-spacer />
            </v-col>
          </v-row>


          <v-data-table-server 
            class="border striped-table"
            :headers="headers"
            :items="items"
            :items-length="total"
            :loading="loading"
            v-model:page="filter.page"
            :items-per-page="filter.length"
            :items-per-page-options="[10, 20, 50, 100]"
            show-select
            v-model:selected="selectedItems"   
            @update:options="loadItems"
          >
 
            <template #item.actions="{ item }">
              <v-btn color="warning" variant="flat" :to="`/user/saleorder/edit/${item.id}`">
                <v-icon>mdi-square-edit-outline</v-icon>
              </v-btn>
              <!-- <v-btn color="danger" variant="flat" class="ml-1" @click="deleteItem(item.id)">
                <v-icon>mdi-delete</v-icon>
              </v-btn> -->
            </template>

 
            <template #item.user="{ item }">
              {{ item.user?.firstName || '-' }} {{ item.user?.surname || '' }}
            </template>

     
            <template #item.status="{ item }">
              <v-chip :color="item.status == 1 ? 'green' : 'red'" size="small" dark>
                {{ item.status == 1 ? 'Active' : 'Deactive' }}
              </v-chip>
            </template>

            <template #item.delivery_note="{ item }">
              {{ item.delivery_note.join(', ') }}
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
      selectedItems: [], 
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
        { title: "Delivery Note", value: "delivery_note" },
        { title: "Ref", value: "ref" },
        { title: "Status", value: "status" },
        { title: "Total", value: "total" },
        { title: "Actions", value: "actions", sortable: false },
      ],
      url :"/api/saleOrder/",
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

      this.items = res.data.map(item => ({ ...item }));       
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
    async deleteSelected() {
      console.log(this.selectedItems)
      if (!this.selectedItems.length) {
        alert("No items selected!");
        return;
      }

      if (!confirm("Are you sure you want to delete selected items?")) return;

      this.loading = true;
      try {
        const ids = this.selectedItems.map(item => item.id);

        for (const id of ids) {
          const deleteurl = this.url + id;
          await generaApi.delete(deleteurl);
        }

        this.$alertStore.add("Selected items deleted successfully", "success");
        this.selectedItems = []; 
        this.loadItems();       

      } catch (error) {
        console.error(error);
        this.$alertStore.add(error.message || "Delete failed", "error");
      } finally {
        this.loading = false;
      }
    },

      reloadPage() {
       this.loadItems();
      }

  },
  
};
</script>
