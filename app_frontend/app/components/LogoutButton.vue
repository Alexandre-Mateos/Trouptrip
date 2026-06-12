<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "LogoutButton",
  methods: {
    logout() {
      sessionStorage.removeItem('token');

      // réinitialiser état utilisateur
      this.userStore.user = null;
      navigateTo('/');
    }
  },
  computed: {
    userStore() {
      return useUserStore()
    },
  },
})
</script>

<template>
  <BaseButton type="button" @click="logout">
    Déconnexion
  </BaseButton>
</template>

<style scoped>

</style>