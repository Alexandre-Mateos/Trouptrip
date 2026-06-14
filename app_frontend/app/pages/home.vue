<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "home",
  computed: {
    userStore() {
      return useUserStore();
    },
    tripsStore() {
      return useTripsStore();
    }
  },
  async mounted(): Promise<any> {
    await this.tripsStore.fetchTrips();
  }
})
</script>

<template>
  <p v-if="userStore.user">Bonjour {{userStore.user.firstname}}</p>
  <ul>
    <li v-for="(trip, index) in tripsStore.tripsList" :key="index">
      {{ trip.title }}
    </li>
  </ul>



</template>

<style scoped>

</style>