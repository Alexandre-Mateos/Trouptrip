<script lang="ts">
import {defineComponent} from 'vue'
import {useTripsStore} from "~/stores/trips";

export default defineComponent({
  name: "trip",
  computed:{
    tripStore() {
      return useTripsStore();
    }
  },
  async mounted() {
    await this.tripStore.fetchTrips();
  }

})
</script>

<template>
<p>Page des séjours</p>
  <template v-for="(trip, index) in tripStore.tripList">
    <TripCard :trip="trip"></TripCard>
  </template>

</template>

<style scoped>

</style>