<template>
  <v-container>
    <v-card
      :loading="loading"
      :disabled="loading"
    >
      <v-card-title>Edit Delivery Note</v-card-title>
      <v-card-subtitle>Update Delivery Noter</v-card-subtitle>
      <v-card-text>
        <v-row class="pt-3">
          <v-col cols="12" sm="4">
            <v-text-field v-model="form.ref" label="Reference" learable persistent-placeholder=""/>
          </v-col>
          <v-col cols="12" sm="4">
            <v-text-field v-model="form.date" type="date" label="Date" learable persistent-placeholder=""/>
          </v-col>
          <v-col cols="12" sm="4">
            <v-text-field v-model="form.remarks" label="Remarks" learable persistent-placeholder=""/>
          </v-col>
          <v-col cols="12" sm="4">
            <v-select
              v-model="form.status"
              :items="config.Status"
              item-title="title"
              item-value="value"
              label="Status"
              learable persistent-placeholder=""
            />
          </v-col>
          <v-col cols="12" sm="4">
            <UserDropdown
              v-model="form.user_id"
              label="User"
              learable persistent-placeholder=""
              disabled
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

    <div class="text-center mt-4">
      <v-btn color="primary" @click="updateForm">
        Update
      </v-btn>
    </div>
  </v-container>
</template>

<script>
import ProductDropdown from "@/components/productDropdown.vue";
import UserDropdown from "@/components/UserDropdown.vue";
import Config from "@/models/config.model";
import generaApi from "@/models/general.model";

export default {
  components: {
    UserDropdown,
    ProductDropdown
  },
  data() {
    return {
      loading: false,
      config:Config,
      url: "/api/deliveryNotes",
      form: {
        date: "",
        ref: "",
        remarks: "",
        status: "",
        discount: 0,
        tax: 0,
        user_id: null,
        items: [],
      },
    };
  },

  mounted() {
    this.id = this.$route.params.id;
    this.fetchData();
  },

  methods: {
    async fetchData() {
      this.loading = true;
      try {
        const id = this.$route.params.id;
        const res = await generaApi.find(this.url, id);
        this.form = {
          date: res.data.date,
          ref: res.data.ref,
          remarks: res.data.remarks,
          status: Number(res.data.status),
          discount: res.data.discount,
          tax: res.data.tax,
          user_id: res.data.user_id,
          items: res.data.items || [],
        };
      } catch (e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    },
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
        product_id: "",
        quantity: 1,
        price: 0,
        discount: 0,
        tax: 0,
      });
    },

    removeItem(index) {
      this.form.items.splice(index, 1);
    },

    async updateForm() {
      this.loading = true;
      try {
          const id = this.$route.params.id;
          const updateUrl = `${this.url}/${id}`;
          const response = await generaApi.put(updateUrl,this.form);
          this.$alertStore.add(response.message, "success");
          setTimeout(() => {
            this.$router.push("/user/deliverynote");
          }, 1000);

      } catch (e) {
          this.$alertStore.add(e.message, "error");
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
  },
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