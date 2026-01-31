<template>
  <v-card :loading="loading" :disabled="loading" 
          title="Expense Category Information" 
          subtitle="Edit Expense Category Item">
        <v-card-text>
        <v-row class="pt-3">
            <!-- Title -->
            <v-col cols="12" sm="12">
            <label class="form-label">Title</label>
            <v-text-field v-model="form.title" placeholder="Enter title" height="38px"/>
            </v-col>
        </v-row>
        </v-card-text>
        <div class="mt-3 text-center">
        <v-btn color="primary"  @click="submitForm">Update</v-btn>
        </div>
  </v-card>
</template>

<script>

import generalModel from "@/models/general.model";

export default {
  data() {
    return {
      loading: false,
      form: {
        title: '',
      },
    };
  },
  computed: {
    
  },
  mounted() {
    this.loadInventory();
  },
  methods: {
    async loadInventory() {

      this.loading = true;
      try {

            const id = this.$route.params.id;
            const res = await generalModel.get('/api/expenseCategory/'+id,this.form);
            const data = res.data;
            this.form.title = data.title;
    
      } catch (error) {
            this.$alertStore.add(error.message,"error");
      } finally {
            this.loading = false;
      }

    },

    async submitForm() {
        
        this.loading = true;
        try {
    
            const id = this.$route.params.id;
            const res = await generalModel.put("/api/expenseCategory/"+id,this.form);
            this.$alertStore.add(res.message, 'success');
            this.$router.push('/user/expensecategory');

        } catch (error) {
            this.$alertStore.add(error.message,'error');
        } finally {
            this.loading = false;
        }

    },

  }
};
</script>

<style scoped>
.form-label {
  font-weight: 500;
  margin-bottom: 4px;
  display: block;
}
</style>
