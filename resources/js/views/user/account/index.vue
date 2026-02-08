<template>
        <v-row>
            <v-col cols="12">
                <v-row class="mt-3">
                    <v-col cols="12" class="">                        
                        <v-card title="Accounts" subtitle="View All Accounts List" class="">
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
                                    <v-col cols="auto" class="py-2">
                                        <v-select label="Group" 
                                            v-model="filter.group"
                                            width="200px"
                                            :items="['customer','employe']"
                                            persistent-placeholder
                                            clearable />
                                    </v-col>
                                    <v-col cols="auto" class="py-2">
                                         <v-text-field label="Search" 
                                            v-model="filter.search"
                                            width="200px"
                                            persistent-placeholder
                                            clearable />
                                    </v-col>
                                    <v-col cols="auto" class="py-2 px-1">
                                        <v-btn color="primary" variant="flat" prepend-icon="mdi-magnify"
                                            @click="loadItems" class="" />
                                    </v-col>
                                     <v-col cols="auto" class="py-2 px-1">
                                        <v-btn class="" color="success" variant="flat"
                                            prepend-icon="mdi-plus" to="/user/account/create"
                                            />
                                     </v-col>
                                    <v-col cols="auto" class="py-2">
                                            Showing {{ from }} - {{ to }}   of {{ totalItems}} Records
                                    </v-col>
                                    <!-- <v-spacer /> -->
                                </v-row>
                             

                                <v-data-table-server class="border striped-table" :headers="headers" :items="items"
                                    :items-length="totalItems" 
                                    :loading="loading" 
                                    item-value="id"
                                    >

                                    <template #item.img="{ item }">
                                    <v-img :src="item.image_preview" width="60" height="50" contain></v-img>
                                    </template>

                                    <template #item.view="{ item }">
                                        <v-btn color="warning" variant="flat" :to="`/user/account/edit/${item.id}`">
                                            <v-icon>mdi-square-edit-outline</v-icon>
                                        </v-btn>
                                        <span class="px-1 py-1"> </span>
                                        <!-- <v-btn color="danger" variant="flat" >
                                            <v-icon>mdi-delete</v-icon>
                                        </v-btn> -->
                                    </template>
                                    <template v-slot:bottom>
                                        <div class="py-2">
                                            <custom-pagination :loading="loading" v-model:page="filter.page"
                                                :lastPage="last_page" @page-changed="loadItems" />
                                        </div>
                                    </template>
                                </v-data-table-server>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
 
</template>

<script>

import generalModel from "@/models/general.model";
import { useGeneralStore } from "@/stores/generalStore";


export default {
    components: {

    },
    data() {
        return {
            filter: {
                search: '',
                length: null,
                page: 1,
                group:null,
            },
            generalStore:useGeneralStore(),
            last_page: 1,
            items: [],
            offset: 0,
            from:0,
            to:0,
            totalItems: 0,
            loading: false,
            headers: [
                { title: "ID", value: "id",sortable: false },
                // { title: "Image", key: "img" , sortable: false },
                { title: "Account", value: "firstName",sortable: false },
                { title: "Group", value: "group",sortable: false },
                { title: "Phone", value: "phone",sortable: false },
                { title: "NIC", value: "nic",sortable: false },
                { title: "Salesman", value: "salesman",sortable: false },
                { title: "City", value: "townCity",sortable: false },
                { title: "Action", key: 'view', sortable: false },
            ],
        };
    },
    computed: {
       
    },
    mounted() {
        this.filter.length = this.generalStore.sort[0];
        this.loadItems();
    },
    methods: {

        async loadItems() {

            this.loading = true;
            try {

                const res = await generalModel.get("/api/users",this.filter);
                this.items = res.data;
                this.totalItems = res.total;
                this.offset = Number(res.offset);
                this.to = Number(res.to);
                this.from = Number(res.from);
                this.filter.page = Number(res.page);
                this.last_page = res.last_page;

            } catch (error) {
          
                this.totalItems = 0;
                this.items = [];
                this.$alertStore.add(error.message, 'error');


            } finally {
                this.loading = false;
            }

        },

    },
};
</script>
