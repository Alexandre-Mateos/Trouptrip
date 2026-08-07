<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "LogoutButton",
  computed: {
    userStore() {
      return useUserStore();
    }
  },
})
</script>

<template>
  <button type="button" @click="userStore.logout" v-bind="$attrs" class="cursor-pointer">
    Déconnexion
  </button>
</template>

<style scoped>

</style>