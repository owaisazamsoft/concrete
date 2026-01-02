<template>
  <v-select
    v-bind="$attrs"
    :model-value="modelValue"
    :items="data"
    item-title="title"
    return-object
    :loading="loading"
    @update:model-value="$emit('update:modelValue', $event)"
  />
</template>

<script>
import ProductsModel from "@/models/product.model";

export default {
  name: "ProductDropdown",

  props: {
    modelValue: {
      type: Object,
      default: null
    }
  },

  emits: ["update:modelValue"],

  data() {
    return {
      data: [],
      loading: false
    };
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
    }
  }
};
</script>
