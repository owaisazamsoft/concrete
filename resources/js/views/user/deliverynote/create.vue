<template>
  <v-container>
    <v-card 
      :loading="loading" 
      :disabled="loading" 
      title="Delivery Note" 
      subtitle="Create Delivery Note">
      <v-card-text>
          <v-row class="pt-3">
            <v-col cols="12" sm="4">
              <v-text-field v-model="form.ref" label="Reference"clearable persistent-placeholder=""/>
            </v-col>
            <v-col cols="12" sm="4">
              <v-text-field v-model="form.date" type="date" label="Date"clearable persistent-placeholder=""/>
            </v-col>
            <v-col cols="12" sm="4">
              <v-text-field v-model="form.remarks" label="Remarks" clearable persistent-placeholder=""/>
            </v-col>
            <v-col cols="12" sm="4">
              <v-select
                v-model="form.status"
                :items="config.Status"
                item-title="title"
                item-value="value"
                label="Status"
                clearable persistent-placeholder=""
              />
            </v-col>
            <v-col cols="3" sm="4">
              <UserDropdown
                    v-model="form.user_id"
                    label="Users" persistent-placeholder=""
                />
            </v-col>
          </v-row>
      </v-card-text>
    </v-card>
    <v-card :loading="loading" :disabled="loading" class="mt-3" title="Item List" >
      <v-card-text >
          <v-row
                v-for="(item, i) in form.items"
                :key="i"
                class="mt-2 align-center" dense >
                <v-col cols="12" md="3">
                  <ProductDropdown
                      :modelValue="item.product_id"
                      @update:value="handleProduct($event,{...item,key:i})"
                      item-title="title"
                      item-value="id"
                      label="Product"
                      return-object
                      persistent-placeholder=""
                    />
                </v-col>
                <v-col cols="6" md="2">
                  <v-text-field
                    v-model.number="item.quantity"
                    label="Qty"
                    type="number"
                    @update:model-value="handleCalculation()"
                    persistent-placeholder=""
                  />
                </v-col>
                <v-col cols="6" md="3">
                  <v-text-field
                    v-model.number="item.price"
                    label="Price"
                    @update:model-value="handleCalculation()"
                    type="number"
                    density="compact"
                  />
                </v-col>
                <v-col cols="6" md="3">
                  <v-text-field
                    :model-value="item.total"
                    label="Total"
                    density="compact"
                    disabled
                  />
                </v-col>
                <v-col cols="6" md="1" class="text-center">
                  <v-btn color="danger" @click="removeItem(i)">X</v-btn>
                </v-col>
              </v-row>
              <div class="mt-4 text-center">
                <v-btn color="primary" variant="tonal" icon="mdi-plus" @click="addItem()" />
              </div>
      </v-card-text>
    </v-card>

    <v-card class="mt-3" title="Total">
      <v-card-text>
        <v-row>
          <v-col cols="12" sm="3">
            <v-text-field label="Total" :model-value="subtotal" disabled />
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <div class="mt-3 text-center" >
        <v-btn color="primary" @click="submitForm">Save</v-btn>
    </div>

  </v-container>
</template>

<script>
import generaApi from "@/models/general.model"
import UserDropdown from "@/components/UserDropdown.vue"
import Config from "@/models/config.model";
import ProductDropdown from "@/components/productDropdown.vue";
import { toRaw } from "vue";


export default {
  components: {
    UserDropdown,
    ProductDropdown
  },

  data() {
    return{
      config:Config,
      loading: false,
      url :"/api/deliveryNotes",
      form: {
        date: "",
        ref: "",
        remarks: "",
        status: 1,
        user_id: null,
        items: [],
        discount: 0,
        tax: 0, 
      },
    };
  },
  methods: {
    handleProduct(product,row){
      this.form.items[row.key].product_id = product.id;
      this.form.items[row.key].price = product.price;
      this.handleCalculation();
    }, 
    handleCalculation(){
      
      let total = 0;
      this.form.items.forEach( (item,key) => {
          this.form.items[key].total = item.price * item.quantity;
      });
    },   
    addItem() {
        this.form.items.push({
          product_id: null,
          quantity: 1,
          price: 0,
          discount: 0,
          tax: 0,
          total:0,
        });
    },
    removeItem(index) {
      this.form.items.splice(index, 1);
      this.handleCalculation();
    },

    async submitForm() {

      this.loading = true;
      try {

          this.loading = false;
          const response = await generaApi.post(this.url,this.form);
          this.$alertStore.add(response.message, "success");
          setTimeout(() => {
            this.$router.push("/user/deliverynote");
          }, 1000);

      } catch (e) {
          this.$alertStore.add(e.message,"error");
          this.loading = false;
      }

    }

  },
  computed: {
    subtotal() {

      return this.form.items.reduce((sum, item) => {
        const qty = Number(item.quantity || 0);
        const price = Number(item.price || 0);
       
        const itemBase = qty * price;
        const itemTotal = itemBase ;

        return sum + itemTotal;
      }, 0);
    },

  }


};
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
