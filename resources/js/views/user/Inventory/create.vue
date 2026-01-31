<template>
  <v-card 
      :loading="loading" 
      :disabled="loading" 
      title="Item Information" 
      subtitle="Create New Item" > 
    <v-card-text>      
        <v-row class="pt-3">
            <v-col cols="12" sm="12">
                <label class="form-label">Title</label>
                <v-text-field v-model="form.title" height="38px" placeholder="Title" />
            </v-col>
        </v-row>
    </v-card-text>
     <div class="mt-3 text-center">
      <v-btn color="primary"  @click="submitForm">Submit</v-btn>
    </div>
  </v-card>
</template>

<script>
import generalModel from '@/models/general.model';


export default {
  data() {
    return {
      loading: false,
      form: {
        title: '',
      },
    }
  },
  computed: {
  
  },
  methods: {
    async submitForm() {

        this.loading = true;
        try {
            let res = await generalModel.post('/api/products',this.form);
            this.$alertStore.add(res.message, 'success');
            this.$router.push(`/user/inventory/edit/${res.data.id}`);

        } catch (error) {
            console.error(error);
            this.$alertStore.add(error.message, 'error');
        } finally {
            this.loading = false;
        }
    },
  }

}
</script>

<style scoped>
.form-label {
  font-weight: 500;
  margin-bottom: 4px;
  display: block;
}
</style>
