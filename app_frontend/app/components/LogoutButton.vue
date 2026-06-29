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

    //   vider le tripsStore pour qu'il se mette à jour à la prochaine reconnexion
      this.tripStore.trips.clear();
    }
  },
  computed: {
    userStore() {
      return useUserStore();
    },
    tripStore(){
      return useTripsStore();
    }
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