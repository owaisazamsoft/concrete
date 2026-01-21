<template>
    <v-card :loading="loading" :disabled="loading" class="" title="Account Information" subtitle="Update User Account Information Carefully">
        <div class="border-b"></div>
        <v-card-text>
            <v-container fluid>
                <v-row class="">
                    <v-col cols="12" md="4">
                        <label class="form-label">Full Name</label>
                        <v-text-field v-model="form.name"/>
                    </v-col>
                    <v-col cols="12" md="4">
                         <label class="form-label">Gender</label>
                        <v-select 
                           v-model="form.gender"
                           :items="['male','female']"
                           />
                    </v-col>
                    <v-col cols="12" md="4">
                        <label class="form-label">Phone</label>
                        <v-text-field v-model="form.phone"/>
                    </v-col>
                     <v-col cols="12" md="4">
                         <label class="form-label">NIC</label>
                        <v-text-field v-model="form.nic" />
                    </v-col>
                    <v-col cols="12" md="4">
                         <label class="form-label">Department</label>
                        <DepartmentDropdown 
                           clearable
                           :modelValue="form.department_id"
                           item-title="title"
                           item-value="id" 
                           @update:value="form.department_id = $event" 
                           />
                    </v-col>
                    <v-col cols="12" md="4">
                        <label class="form-label">Group</label>
                        <GroupDropdown 
                           clearable
                           :modelValue="form.group_id"
                           item-title="title"
                           item-value="id" 
                           @update:value="form.group_id = $event" 
                           />
                    </v-col>
                     <v-col cols="12" md="4">
                        <label class="form-label">Date Of Birth</label>
                        <v-text-field type="date" v-model="form.dob"/>
                    </v-col>
                    <v-col cols="12" md="4">
                        <label class="form-label">Street Address</label>
                        <v-text-field v-model="form.address"/>
                    </v-col>
                </v-row>
            </v-container>

            <div class="mt-3 text-center" >
                <v-btn color="primary" @click="submit">Save</v-btn>
            </div>
        </v-card-text>
        
    </v-card>
</template>
<script>
import DepartmentDropdown from '@/components/DepartmentDropdown.vue';
import GroupDropdown from '@/components/GroupDropdown.vue';
import generalModel from '@/models/general.model';
import UserModel from '@/models/user.model';
import helper from '@/plugins/hleper';
import { toRaw } from 'vue';



export default {
    components:{
        DepartmentDropdown,
        GroupDropdown
    },
    data() {
        return {
            loading: true,
            helper: helper,
            form: {
                id:'',
                name:'',
                gender:null,
                phone:'',
                dob:null,
                department_id:null,
                nic:'',
                group_id:null,
                address:'',
            },
            edit: false,
        };
    },
    computed: {


    },
  async mounted() {
        
   

        this.loadDataFromProfile()
    },
    methods: {

        async loadDataFromProfile() {
            this.loading = true;
            try {

                let response = await generalModel.get('/api/users/'+this.$route.params.id,{});
                this.form.name = response.data.name;
                this.form.gender = response.data.data?.gender;
                this.form.phone = response.data.data?.phone;
                this.form.nic = response.data.data?.nic;
                this.form.dob = response.data.data?.dob;
                this.form.address = response.data.data?.address;
                this.form.department_id = Number(response.data.data?.department_id);
                this.form.group_id = Number(response.data.data?.group_id);
               
          
          
                this.loading = false;
            } catch (error) {
                this.$alertStore.add(error.message, 'error');
                this.loading = false;
            }
        },
        async submit() {
            this.loading = true;
            try {

                const response = await generalModel.put('/api/users/'+this.$route.params.id,this.form);
                this.$alertStore.add('Profile Updated', 'success');
                this.loading = false;
                this.loadDataFromProfile()
            } catch (error) {      
                this.$alertStore.add(error.message, 'error');
                this.loading = false;
            }
        },
    
    },

};
</script>


<style></style>