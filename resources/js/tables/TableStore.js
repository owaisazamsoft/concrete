import { defineStore } from "pinia";
import api from "../plugins/axios";
import generaApi from "@/models/general.model"

import { errorHandler } from "@/services/responseHandleService";

export const useTableStore = defineStore("table", {
    state: () => ({
        loading:false,
        data:[],
        total:0,
        lastPage:1,
        page:1,
        offset:1,
        length:10,
    }),
    getters: {

    },
    actions: {
        toggleFilter() {
            this.vehichleDetail.sidebar = !this.vehichleDetail.sidebar
        },
       async DeleteRecord(url) {
            
            if (!confirm("Are you sure you want to delete selected items?")) return ;
                this.loading = true;
                try {
                    await generaApi.delete(url);
                    // this.$alertStore.add("Selected items deleted successfully", "success");
                    this.loading = true;
                    return true;
                } catch (error) {
                    // this.$alertStore.add(error.message || "Delete failed", "error");
                    return false;
                } 

        },
        
    },

});
