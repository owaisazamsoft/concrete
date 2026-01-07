import { defineStore } from "pinia";
import api from "../../plugins/axios";
import generaApi from "@/models/general.model"
import * as XLSX from 'xlsx';
import { saveAs } from 'file-saver';
import { errorHandler } from "@/services/responseHandleService";

export const useTableStore = defineStore("table", {
    state: () => ({
        loading:false,
        searchUrl:"",
        data:[],
        total:0,
        lastPage:1,
        page:1,
        from:0,
        to:0,
        length:10,
    }),
    getters: {

    },
    actions: {
      
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
                finally {
                    return false;
                }
        },

        async downloadAllExcel(apiUrl, headers) {
            try {
                let allData = [], page = 1, lastPage = 1;
                do {
                const params = { page, length: 100 };
                const res = await generaApi.get(apiUrl, params);
                allData = allData.concat(res.data);
                lastPage = res.last_page;
                page++;
                } while (page <= lastPage);

                if (!allData.length) return alert('No data found');

                allData.reverse();

                const rows = allData.map(inv => {
                const row = {};
                headers.forEach(h => {
                    if (h.value === 'user') row[h.title] = inv.user?.firstName || '-';
                    else if (h.value === 'is_paid') row[h.title] = inv.is_paid == 1 ? 'Paid' : 'Unpaid';
                    else if (h.value === 'status') row[h.title] = inv.status == 1 ? 'Active' : 'Deactive';
                    else row[h.title] = inv[h.value] ?? '';
                });
                return row;
                });

                const ws = XLSX.utils.json_to_sheet(rows);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Data');

                saveAs(new Blob([XLSX.write(wb, { bookType:'xlsx', type:'array' })], { type:'application/octet-stream' }), 'Data.xlsx');

            } catch (err) {
                console.error('Excel generation failed', err);
            }
        
        },
    

    }

});


