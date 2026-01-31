<template>
 
  <v-autocomplete
    v-bind="$attrs"
    :model-value="value"
    :items="data"
    :loading="loading"
    @update:model-value="$emit('update:value',$event)"
  />
</template>


<script>
import generaApi from "@/models/general.model"

export default {
  name: "DeliveryNoteDropdown",
  props: {
    value: {
      type: [Number, String,Boolean],
      default: null
    },
    userId: {
      type: [Number, String],
      default: null
    }
  },

  data() {
    return {
      data: [],
      loading: false,
      url: "/api/deliveryNotes"
    }
  },

  watch: {
    userId: {
      immediate: true,
      handler() {
        this.getData()
      }
    }
  },
  emits: ['update:value'],
  methods: {
    async getData() {
      
      // if (!this.userId) {
      //   this.data = []
      //   return
      // }

      this.loading = true
      try {
        const res = await generaApi.all(this.url, {
          length: 1000,
          user_id: this.userId
        })

        this.data = res.data;

      } catch (e) {
        console.error("Delivery note load failed", e)
        this.data = []
      } finally {
        this.loading = false
          this.$emit("update:loaded",true);
      }
    },

  }


}
</script>


<style scoped>
    
</style>