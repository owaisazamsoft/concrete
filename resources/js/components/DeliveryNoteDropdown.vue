<template>
  <v-autocomplete
    v-bind="$attrs"
    :model-value="modelValue"
    :items="data"
    :loading="loading"
    @update:model-value="handleValue"
  />
</template>


<script>
import generaApi from "@/models/general.model"

export default {
  name: "DeliveryNoteDropdown",
  props: {
    modelValue: {
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

  methods: {
    async getData() {
      
      if (!this.userId) {
        this.data = []
        return
      }

      this.loading = true
      try {
        const res = await generaApi.all(this.url, {
          length: 1000,
          user_id: this.userId
        })

        this.data = res.data.map(item => ({
          id: item.id,
          title:item.ref +' - '+ item.prefix,
          total: Number(item.total || 0)
        }))

      } catch (e) {
        console.error("Delivery note load failed", e)
        this.data = []
      } finally {
        this.loading = false
          this.$emit("update:loaded",true);
      }
    },

    handleValue(val) {
      this.$emit("update:value", val)
    }
  }
}
</script>


<style scoped>
    
</style>