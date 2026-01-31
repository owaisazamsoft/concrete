<template>
  <v-container>
    <v-card :loading="loading" :disabled="loading"
      title="Sale Invoice"
      subtitle="Create Sale Invoice"
    >
      <v-card-text>
        <v-row class="pt-3">
          <v-col cols="12" sm="4">
            <v-text-field v-model="form.ref" label="Reference" />
          </v-col>
          <v-col cols="12" sm="4">
            <v-text-field v-model="form.date" type="date" label="Date" />
          </v-col>
          <v-col cols="12" sm="4">
            <v-text-field v-model="form.remarks" label="Remarks" />
          </v-col>
          <v-col cols="12" sm="4">
            <v-select
              v-model="form.status"
              :items="generalStore.status"
              item-title="title"
              item-value="value"
              label="Status"
            />
          </v-col>
               <v-col cols="3" sm="4">
                <v-select 
                  v-model="form.is_paid" 
                  :items="generalStore.PaymentStatus" 
                  item-title="title" 
                  item-value="value"  
                  label="Paid status" 
                  clearable 
                  persistent-placeholder=""/>
              </v-col>
              <v-col cols="12" sm="4">
                <UserDropdown  v-model="form.user_id" label="User" />
              </v-col>
        </v-row>
      </v-card-text>
    </v-card>


    <!-- ITEMS -->
    <v-card class="mt-3" title="Item List">
      <v-card-text>
        <v-row v-for="(item,i) in form.items" :key="i" class="mt-2">
          <v-col cols="8">
            <v-autocomplete
              :items="dcItems"
              :model-value="item.delivery_note_id"
              clearable
              item-title="titleWithRef"
              item-value="id"
              return-object
              label="Delivery Note"
              @update:model-value="handleDeliveryNote($event,{...item,key:i})"
             
            />
          </v-col>
          <v-col cols="3">
            <v-text-field
              v-model="item.total"
              label="Total"
              density="compact"
              disabled
            />
          </v-col>

          <v-col cols="1">
            <v-btn color="danger" @click="removeItem(i)">X</v-btn>
          </v-col>

        </v-row>

        <div class="mt-4 text-center">
          <v-btn
            color="primary"
            variant="tonal"
            icon="mdi-plus"
            @click="addItem"
          />
        </div>
      </v-card-text>
    </v-card>

    <!-- TOTAL -->
    <v-card class="mt-3" title="Invoice Total">
      <v-card-text>
        <v-row>
          <!-- <v-col cols="3">
            <v-text-field label="Subtotal" :model-value="form.subtotal" disabled />
          </v-col>
          <v-col cols="3">
            <v-text-field  type="number" label="Discount" v-model="form.discount" @update:model-value="handleCalculation" />
          </v-col>
          <v-col cols="3">
            <v-text-field type="number" label="Tax" v-model="form.tax" @update:model-value="handleCalculation" />
          </v-col> -->
          <v-col cols="3">
            <v-text-field label="Grand Total" :model-value="form.total" disabled />
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <div class="mt-4 text-center">
      <v-btn color="primary" @click="submit">Submit</v-btn>
    </div>

  </v-container>
</template>


<script>
import UserDropdown from "@/components/UserDropdown.vue"
import DeliveryNoteDropdown from "@components/DeliveryNoteDropdown.vue";
import generalModel from "@/models/general.model";
import { useGeneralStore } from "@/stores/generalStore";

export default {
  components: {
    UserDropdown,
    DeliveryNoteDropdown
  },
  data() {
    return {
      generalStore:useGeneralStore(),
      loading: false,
      dcItems:[],
      form: {
        user_id: null,
        date: "",
        ref: "",
        remarks: "",
        status: 1,
        is_paid: 0,
        items: [],
        subtotal:0,
        discount: 0,
        tax: 0,
        total:0,
      },
    }
  },
  mounted() {
    this.handleCalculation();
  },
  methods: {
    handleDeliveryNote(model,item){
      if(!model){
        this.form.items[item.key].delivery_note_id = null;
        this.form.items[item.key].total = 0;
        this.handleCalculation();
        return false;
        
      }
      
      this.form.items[item.key].delivery_note_id = model.id;
      this.form.items[item.key].total = model.total;
      this.handleCalculation()
    },
    addItem() {
      this.form.items.push({
        delivery_note_id: null,
        total:0,
      })
      this.handleCalculation()
    },
    removeItem(i) {
      this.form.items.splice(i, 1)
      this.handleCalculation()
    },
    handleCalculation(){

      let subtotal = 0;
      this.form.items.forEach((item,key) => {
        subtotal += parseFloat(item.total);
      });
      
      this.form.subtotal = subtotal;
      subtotal  = subtotal - parseFloat(this.form.discount);
      subtotal  = subtotal + parseFloat(this.form.tax);
      this.form.total = subtotal;
    },

    async checkDuplicate() {
        let items = this.form.items;
        let hasDuplicate = false;

        for (let i = 0; i < items.length; i++) {
          for (let j = i + 1; j < items.length; j++) {
            if (items[i].delivery_note_id && items[i].delivery_note_id === items[j].delivery_note_id) {
               hasDuplicate = true;
              break;
            }
          }
        }

        if (hasDuplicate) {
       
          return true; // Yes, there are duplicates
        }

        return false; // No duplicates found
    },
    async submit() {

      const hasDuplicate = await this.checkDuplicate();
      if (hasDuplicate === true) {
        this.$alertStore.add("Duplicate Record Found", "error");
        return false; 
      }

      this.loading = true
      try {
          let res = await generalModel.post("/api/saleInvoice",this.form)
          this.$alertStore.add(res.message,"success");
          this.loading = false;
           this.$router.push("/user/saleInvoice");
      } catch (e) {
          this.$alertStore.add(e.message, "error");
          this.loading = false;
      }
    }
  },

  computed: {

  },
  watch: {
    'form.user_id': {
        immediate: true,
         async handler(newVal, oldVal) {

              if(!newVal){
                this.dcItems = [];
                return false;
              }

              try {
                  let items = await generalModel.get('/api/deliveryNotes',{user_id:newVal,length:10000})
                  this.dcItems = items.data;
              } catch (error) {
                  this.dcItems = [];
                  alert(error.message);
              }   

          }
      }
  },
}
</script>


<style>
.section {
  margin-bottom: 28px;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
}
</style>
