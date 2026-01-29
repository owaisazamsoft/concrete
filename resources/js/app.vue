<template>
      <div>
          <router-view></router-view>
          <Alert />
      </div>
</template>

<script>

import { useUserStore } from './stores/userStore';
import Alert from '@components/alert.vue'
import Overlay from '@components/overlay.vue'
import { toRaw } from 'vue'
import { useTheme } from 'vuetify';

export default {
  props: {},
  components: {
    Alert,
    Overlay
  },
  data() {
    return {
      userStore: useUserStore(),
    };
  },
  methods: {

  },
  async mounted() {

    

  },
};

</script>