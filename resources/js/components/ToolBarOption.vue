<template>
     <v-menu offset-y>
            <template v-slot:activator="{ props }">
            <v-btn icon v-bind="props">
                <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
            </template>
            <v-list>
                <slot></slot>
            
            </v-list>
        </v-menu>
</template>

<script>
    
export default {
  name:"ToolBarOption",
  components: {

  },
  data() {
    return {

    }
  },
  mounted() {
   
  },
  methods: {
  },
  
};
</script>


<style>


</style>