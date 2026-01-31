<template>
  <v-card :loading="loading" :disabled="loading" 
          title="Item Information" 
          subtitle="Edit Item">
    <v-card-text>
      <v-row class="pt-3">
        <!-- Title -->
        <v-col cols="12" sm="6">
          <label class="form-label">Title</label>
          <v-text-field v-model="form.title" placeholder="Enter inventory title" height="38px"/>
        </v-col>

        <!-- SKU -->
        <v-col cols="12" sm="6">
          <label class="form-label">SKU</label>
          <v-text-field v-model="form.sku" placeholder="Enter SKU" height="38px"/>
        </v-col>

        <!-- Price -->
        <v-col cols="12" sm="4">
          <label class="form-label">Price</label>
          <v-text-field v-model="form.price" type="number" placeholder="Enter price" height="38px"/>
        </v-col>

        <!-- Unit -->
        <v-col cols="12" sm="4">
          <label class="form-label">Unit</label>
          <UnitDropdown 
            v-model="form.unit_id"
            clearable  
            placeholder="Select Unit" />
        </v-col>

        <v-col cols="12" sm="4">
          <label class="form-label">Category</label>
          <CategoryDropdown 
            v-model="form.category_id"
            clearable  
            placeholder="Select Category" />
        </v-col>

        <v-col cols="12" sm="6">
          <label class="form-label">Image</label>
          <v-file-input
            v-model="form.image"
            label="Upload Image"
            prepend-icon="mdi-file"
            variant="filled"
            accept="image/*"
          />
        </v-col>

        <!-- Image preview -->
        <v-col cols="12" sm="6" style="margin-top: 20px;">
          <v-img v-if="imagePreview" :src="imagePreview" width="100" height="80" contain />
        </v-col>

        <!-- Description -->
        <v-col cols="12">
          <label class="form-label">Description</label>
          <v-textarea variant="outlined" v-model="form.description" rows="3" placeholder="Enter description" />
        </v-col>
      </v-row>
    </v-card-text>

     <div class="mt-16 text-center">
      <v-btn color="primary" @click="submitForm">Update</v-btn>
    
   </div>
  </v-card>
</template>

<script>
import CategoryDropdown from "@/components/CategoryDropdown.vue";
import UnitDropdown from "@/components/UnitDropdown.vue";
import generalModel from "@/models/general.model";

export default {
  components:{
    CategoryDropdown,
    UnitDropdown
  },
  data() {
    return {
      loading: false,
      form: {
        title: '',
        sku: '',
        price: '',
        unit_id: null,
        category_id: null,
        description: null,
        image: null,
      },
      originalImage: null,
    };
  },
  computed: {
    imagePreview() {
      if (this.form.image) {
        return typeof this.form.image === 'string' ? this.form.image : URL.createObjectURL(this.form.image);
      }
      return this.originalImage;
    }
  },
  mounted() {
    this.loadInventory();
  },
  methods: {
    async loadInventory() {

      this.loading = true;
      try {

          const id = this.$route.params.id;
          const res = await generalModel.get('/api/products/'+id,{});
          const data = res.data;

          this.form.title = data.title;
          this.form.sku = data.sku;
          this.form.price = data.price;
          this.form.unit_id = data.unit_id;
          this.form.category_id = data.category_id;
          this.form.description = data.description;
          this.originalImage = data.image_preview;

      } catch (error) {
          console.error(error);
          this.$alertStore.add(error.message, "error");
      } finally {
          this.loading = false;
      }

    },



    async submitForm() {

        this.loading = true;
        try {

            if (this.form.image instanceof File) {
                if(this.form.image.type != "image/png"){
                   this.$alertStore.add("Invalid Extension Allow Only",'error');
                   return false;
                }

                const maxSize = 500 * 1024;
                if(this.form.image.size > maxSize){
                   this.$alertStore.add("Image Size Can Not Be Greater Than 500kb",'error');
                   return false;
                }

            }

            const id = this.$route.params.id;
            const res = await generalModel.put('/api/products/'+id,this.form);
            this.$alertStore.add(res.message, 'success');
            this.$router.push(`/user/inventory`);
            
          
        } catch (error) {
            this.$alertStore.add(error.message,'error');
        } finally {
            this.loading = false;
        }
    },


    resetForm() {
      this.loadInventory();
    }
  }
};
</script>

