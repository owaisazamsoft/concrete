<template>
    <v-autocomplete
        v-bind="$attrs"
        :model-value="modelValue"
        :items="data"
        item-title="title"
        item-value="id"
        :loading="loading"
        @update:model-value="$emit('update:value',$event)"
        />  
</template>
<script>
import generalModel from '@/models/general.model';

export default {
    name: "DepartmentDropdown",
    props: {
        modelValue: {
            type: [String,Number,Boolean],
            default: null
        }, 
    },
    data() {
        return {
            data: [],
            loading: false,
        };
    },
    mounted(){
        this.getData();
    },
    methods: {
        async getData() {
                this.loading = true;
                try {
                    const response = await generalModel.get('/api/posts/',{type:'department',length:1000});
                    this.data = response.data; 
                } catch (err) {
                    console.error("Error loading platforms:", err);
                    this.data = [];
                } finally {
                    this.loading = false;
                }
        },
    },
    emits: ['update:value']
};
</script>

<style scoped>
    
</style>