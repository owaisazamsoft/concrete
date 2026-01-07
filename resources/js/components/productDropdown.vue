<template>
    <v-autocomplete
      v-bind="$attrs"
      :model-value="modelValue"
      :items="data"
      :loading="loading"
      @update:model-value="handleValue($event)"
    />
</template>

<script>
import ProductsModel from "@/models/product.model";

export default {
  name: "ProductDropdown",
  props: {
    modelValue: {
      type: [String, Number,Boolean],
      default: null
    }
  },
  data() {
    return {
      value:null,
      data: [],
      loading: false
    };
  },
  watch:{  
    modelValue:{
      immediate: true,
      handler(newValue,oldValue){

      }
    }
  },
  mounted() {
    this.getData();
  },
  methods: {
    async getData() {
        this.loading = true;
        const res = await ProductsModel.all({ length: 1000 });
        this.data = res.data;
        this.loading = false;
    },
    handleValue(value) {
        this.$emit("update:value", value);
    },
  },
  emits: ["update:value"],
};
</script>
