<template>
    <v-card :loading="loading" :disabled="loading" class="" title="Account Information" subtitle="Update User Account Information Carefully">
        <div class="border-b"></div>
        <v-card-text>
            <v-container fluid>
                <!-- <v-row>
                    <v-col cols="12">
                        <div class="d-flex align-center">
                            <div class="pr-2">
                                <img v-if="form?.avatar?.name" style="width:100px;height: 100px;" class="border"
                                    :src="helper.imageFileToUrl(form?.avatar)" />
                                <img v-else style="width:100px;height: 100px;" class="border" :src="form?.avatar" />
                            </div>
                            <div class="pl-3 pt-3">
                                <v-btn color="primary" @click="this.$refs.fileInput.click();" class=" text-capitalize"
                                    variant="flat">Update New Photo</v-btn>
                                <p class="pt-3 text-light text-body-2">Avatar Allowed JPG, GIF or PNG. Max size of 800K
                                </p>
                                <v-file-input ref="fileInput" clearable class="d-none" @change="hInput($event, 'avatar')"
                                    label="File input" density="comfortable" variant="filled" accept="image/*"
                                    color="primary" prepend-icon="mdi-image" />
                            </div>
                        </div>
                    </v-col>
                </v-row> -->
                <v-row class="">
                    <v-col cols="12" md="4">
                        <label class="form-label">Full Name</label>
                        <v-text-field v-model="form.firstName" />
                    </v-col>
                    <v-col cols="12" md="4">
                        <label class="d-block pb-2">Phone</label>
                        <v-text-field v-model="form.phone" />
                    </v-col>
                    <v-col cols="12" md="4">
                        <label class="d-block pb-2">Email</label>
                        <v-text-field v-model="form.personalEmail" />
                    </v-col>
                     <v-col cols="12" md="4">
                        <label class="d-block pb-2">NIC</label>
                        <v-text-field v-model="form.nic" />
                    </v-col>
                    <v-col cols="12" md="4">
                        <label class="d-block pb-2">NTN</label>
                        <v-text-field v-model="form.ntn" />
                    </v-col>
                    <v-col cols="12" md="4">
                        <label class="d-block pb-2">Salesman</label>
                        <v-text-field v-model="form.salesman" />
                    </v-col>
                    <v-col cols="12" md="4">
                        <label class="d-block pb-2">Group</label>
                        <v-select v-model="form.group" :items="['customer','employe']" />
                    </v-col>
                    <v-col cols="12" md="4">
                        <label class="form-label">Country</label>
                        <v-text-field v-model="form.country" />
                    </v-col>
                    <v-col cols="12" md="4">
                        <label class="form-label">City</label>
                        <v-text-field v-model="form.townCity" />
                    </v-col>
                    <v-col cols="12" md="4">
                        <label class="form-label">Street Address</label>
                        <v-text-field v-model="form.companyAddress1"  />
                    </v-col>
                </v-row>
            </v-container>
        </v-card-text>
        <div class="mt-3 text-center" >
            <v-btn color="primary" @click="onSubmit">Save</v-btn>
        </div>
    </v-card>
</template>
<script>
import generalModel from '@/models/general.model';
import helper from '@/plugins/hleper';

export default {
    data() {
        return {
            loading: true,
            helper: helper,
            form: {
                id:'',
                firstName:'',
                phone:'',
                personalEmail:'',
                country:'',
                townCity:'',
                companyAddress1:'',
                salesman:'',
                nic:'',
                ntn:'',
                group:'',  
            },
            edit: false,
        };
    },
    computed: {


    },
    mounted() {
        this.loadDataFromProfile()
    },
    methods: {
        async loadDataFromProfile() {
        
            // this.$refs.fileInput.value = null;
            this.loading = true;
            const id = this.$route.params.id;
            generalModel.get('/api/users/'+id,{}).then((res) => {
                
                let data = res.data.user;

                this.form.id = data?.id;
                this.form.firstName = data?.firstName,
                this.form.phone = data?.phone,
                this.form.personalEmail = data?.personalEmail,
                this.form.country = data?.country,
                this.form.townCity = data?.townCity,
                this.form.companyAddress1 = data?.companyAddress1,
                this.form.salesman = data?.salesman,
                this.form.nic = data?.nic,
                this.form.ntn = data?.ntn,
                this.form.group = data?.group,
                this.loading = false;

            }).catch((error) => {
                this.loading = false;
            })

        },
        onSubmit() {

            this.loading = true;
            const id = this.$route.params.id;
            generalModel.put("/api/users/"+id,this.form).then((res) => {

                this.loadDataFromProfile();
                this.loading = false;
                this.$alertStore.add('Profile Updated', 'success');
                this.$router.push('/user/account');

            }).catch((error) => {
                this.loading = false;
                this.$alertStore.add(error.message, 'error');
            });

        }
    },

};
</script>


<style></style>