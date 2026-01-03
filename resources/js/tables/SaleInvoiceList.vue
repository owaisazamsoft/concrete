<template>
  <v-row>
    <v-col cols="12">
      <v-card>

        <v-card-title class="d-flex align-center justify-space-between">
          <div>
            <div class="text-h6">{{title}}</div>
            <div class="text-caption text-grey">{{ subTitle }}</div>
          </div>
          <v-menu location="bottom end">
            <template #activator="{ props }">
              <v-btn icon="mdi-dots-vertical" variant="text" v-bind="props" />
            </template>
            <v-list>
              <v-list-item v-for="(op) in actions.filter(r => r.isTop == 1)">
                        <v-list-item-title 
                          @click="handleRowAction(op,{})">
                          <div>
                              <v-icon 
                                v-if="op.icon" 
                                size="18" 
                                class="me-2" 
                                :color="op.color">{{ op.icon }}</v-icon>
                              <span v-if="op.title">{{ op.title }}</span>
                          </div>
                        </v-list-item-title>
                </v-list-item>
            </v-list>
          </v-menu>
        </v-card-title>

        <v-card class="" outlined>
          <v-card-text>
            <v-row class="flex-row" align="center" >
                <v-col 
                  v-for="field in fields"
                    :key="field.name"
                    cols="auto"
                    
                  >
                  <UserDropdown v-if="field.name == 'user_id'" 
                    v-bind="field.props"
                    v-model="field.value" 
                  
                  />

                  <v-btn v-else-if="field.component == 'v-btn'" 
                         v-bind="field.props" >
                    {{ field.props.label }}
                  </v-btn>
                  
                  <component v-else
                    :is="field.component"
                    v-model="field.value"
                    v-bind="field.props"
                    
                  />
              </v-col>
              <v-col>
                <div class="align-self-center">
                  Showing {{ filter.offset + 1 }} - {{ Math.min(filter.offset + items.length, total) }} of {{ total }}
                  Records
                </div>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
        <v-card-text>
          <v-row class="mb-2">
            <v-col cols="12" class="d-flex align-center flex-wrap">
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
            @update:options="loadItems">

            <template #item.actions="{ item }">
                <v-menu offset-y>
                  <template v-slot:activator="{ props }">
                    <v-btn icon v-bind="props">
                      <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                  </template>
                  <v-list>
                  <v-list-item v-for="(op) in actions.filter(r => !r?.isTop)">
                        <v-list-item-title 
                          @click="handleRowAction(op,item)">
                          <div>
                              <v-icon 
                                v-if="op.icon" 
                                size="18" 
                                class="me-2" 
                                :color="op.color">{{ op.icon }}</v-icon>
                              <span v-if="op.title">{{ op.title }}</span>
                          </div>
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item>
                       <v-list-item-title>
                          <router-link target="_blank" :href="'/api/saleInvoice/print/'+item.id" >
                            <div>
                                  <v-icon  
                                    size="18" 
                                    class="me-2" 
                                    color="success">mdi-printer</v-icon>
                                  <span>Print</span>
                              </div>
                          </router-link>
                        </v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
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

            <template #item.delivery_note="{ item }">
                <div v-if="item.items?.length">
                  <div v-for="(it, index) in item.items" :key="index">
                    {{ it.delivery_note?.ref || '-' }}
                  </div>
                </div>
                <span v-else>-</span>
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
import generaApi from "@/models/general.model"
import UserDropdown from "@/components/UserDropdown.vue"
import Config from "@/models/config.model";


export default {
  name:"SaleInvoiceList",
  components: {
    UserDropdown
  },
  data() {
    return {
      config:Config,
      title:'Sale Invoice',
      subTitle:'View All Sale Invoice Details',
      fields:[
        {
          name: 'start_date',
          value:'',
          component: 'v-text-field',
          props: {
            type:'date',
            label: 'Start Date',
            clearable: true
          },
        },
        {
          name: 'end_date',
          value:'',
          component: 'v-text-field',
          props: {
            type:'date',
            label: 'End Date',
            clearable: true
          },
        },
        {
          name: 'user_id',
          value:null,
          component: 'v-text-field',
          props: {
            label: 'Users',
            clearable: true,
            minWidth:'200px',
            persistentPlaceholder:true,
          },
        },
        {
          name: 'status',
          value:null,
          component: 'v-select',
          props: {
            label: 'Status',
            clearable: true,
            items:Config.Status,
            minWidth:'250px',
            persistentPlaceholder:true,
          },
        },
        {
          name: 'search',
          value:'',
          component: 'v-text-field',
          props: {
            prependInnerIcon:"mdi-magnify",
            label: 'Search',
            clearable: true,
            minWidth:"300px",
            persistentPlaceholder:true,
          },
        },
        {
          name: 'length',
          value:10,
          component: 'v-select',
          props: {
            label: 'Length',
            items:Config.Sorts,
            persistentPlaceholder:true,
          },
        },
        {
          name: 'sort',
          value:'',
          component:'v-btn',
          props: {
            title:'ssss',
            label: 'Search',
            color:'primary',
            persistentPlaceholder:true,
          },
        },

      ],
      filter: {
        search: "",
        length: 10,
        page: 1,
        offset: 0,
        start_date: "",
        end_date: "",
        user_id: null,
        status: null
      },
      items: [],
      total: 0,
      last_page: 1,
      loading: false,
      actions:[
        {
          title:'Create',
          icon:'mdi-plus',
          color:'success',
          isTop:1,
        },
        {
          title:'Reload',
          icon:'mdi-reload',
          color:'danger',
          isTop:1,
        },
        {
          title:'Download',
          icon:'mdi-printer',
          color:'success',
          isTop:1,
        },
        // {
        //   title:'Print',
        //   icon:'mdi-printer',
        //   color:'success',
        // },
        {
          title:'Edit',
          icon:'mdi-printer',
          color:'warning',
        },
        {
          title:'Delete',
          icon:'mdi-delete',
          color:'danger',
        }
      ],
      headers: [
        { title: "ID", value: "id" },
        { title: "Date", value: "date" },
        { title: "Invoice No", value: "prefix" },
        { title: "User", value: "user" },
        { title: "Paid Status", value: "is_paid" },
        { title: "Status", value: "status" },
        { title: "Total", value: "total" },
        { title: "Actions", value: "actions", sortable: false },
      ],
      url: "/api/saleInvoice/",
    };
  },
  mounted() {
    // this.loadItems();
  },
  methods: {

    async loadItems() {

        this.loading = true;

        try {
            const res = await generaApi.get(this.url, this.filter);
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

    async handleRowAction(action,item){
      switch (action.title) {
        case 'Create':
          this.$router.push('/user/saleInvoice/Create')
          break;
        case 'Print':
          window.open('api/saleInvoice/' + item.id, '_blank');
          break;
        case 'Edit':
          this.$router.push('/user/saleInvoice/edit/'+item.id)
          break;
        case 'Reload': 
          this.loadItems()
          break;
        case 'Delete':
          this.deleteSelected(item.id);    
          break;
      
        default:
          break;
      }
    },
    async deleteSelected(id) {
        
          if (!confirm("Are you sure you want to delete selected items?")) return;
          this.loading = true;

          try {
              await generaApi.delete('/api/saleInvoice/'+id);
              this.$alertStore.add("Selected items deleted successfully", "success");
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
