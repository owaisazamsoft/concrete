<template>
  <v-card
    :loading="loading"
    :disabled="loading"
    title="Expense Information"
    subtitle="Edit Expense Item"
  >
    <v-card-text>
      <v-row class="pt-3">

        <!-- Date -->
        <v-col cols="12" sm="6">
          <label class="form-label">Date</label>
          <v-text-field v-model="form.date" type="date" />
        </v-col>

        <!-- Debit -->
        <v-col cols="12" sm="6">
          <label class="form-label">Debit</label>
          <v-text-field v-model="form.debit" type="number" />
        </v-col>

        <v-col cols="12" sm="6">
          <label class="form-label">Credit</label>
          <v-text-field v-model="form.credit" type="number" />
        </v-col>

        <!-- Category -->
        <v-col cols="12" sm="6">
          <label class="form-label">Category</label>
          <ExpenseCategory 
            v-model="form.category_id"
            clearable  
            placeholder="Select category" />
        </v-col>

        <!-- Remarks -->
        <v-col cols="12">
          <label class="form-label">Remarks</label>
          <v-textarea v-model="form.remarks" rows="3" />
        </v-col>

      </v-row>
    </v-card-text>

     <div class="mt-16 text-center">
      <v-btn color="primary"  @click="submitForm">Update</v-btn>
    </div>
  </v-card>
</template>


<script>
import ExpenseCategory from "@/components/ExpenseCategory.vue";
import generalModel from "@/models/general.model";

export default {
 components:{ExpenseCategory},
  data() {
    return {
      loading: false,
      categories: [],
      form: {
        date: "",
        debit: "",
        credit:"",
        remarks: "",
        category_id: null,
      },
    };
  },

  mounted() {
   
    this.loadExpense();
  },

  methods: {

    async loadExpense() {
      this.loading = true;
      try {

        const id = this.$route.params.id;
        const res = await generalModel.get('/api/expenses/'+id,{});
        const data = res.data;
        this.form.date = data.date;
        this.form.debit = data.debit;
        this.form.remarks = data.remarks;
        this.form.category_id = data.category_id;

      } catch (e) {
        this.$alertStore.add(e.message, "error");
      } finally {
        this.loading = false;
      }
    },

    async submitForm() {

      this.loading = true;
      try {
        const id = this.$route.params.id;
        const res = await generalModel.put("/api/expenses/"+id,this.form);
        this.$alertStore.add(res.message, "success");
        this.$router.push("/user/expense");

      } catch (error) {
        this.$alertStore.add(error.message, "error");
      } finally {
        this.loading = false;
      }
    },

  
  },
};
</script>


<style scoped>
.form-label {
  font-weight: 500;
  margin-bottom: 4px;
  display: block;
}
</style>
