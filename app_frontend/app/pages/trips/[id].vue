<script lang="ts">
import {defineComponent} from 'vue'
import {useTripsStore} from "~/stores/trips";
import type {IMapTripDetails} from "~/interfaces/i-mapTripDetails";

export default defineComponent({
  name: "[id]",
  data() {
    return {
      id: null as number | null,
      tripStore: useTripsStore(),
    }
  },
  mounted() {
    const idParam = useRoute().params.id;
    if (idParam) {
      this.id = Number(idParam);
      this.tripStore.fetchTrip(this.id);
    }
  },
  computed: {
    trip() {
      if (this.id === null) return null;
      return this.tripStore.getTripById(this.id) as IMapTripDetails | undefined;
    }
  }
})
</script>

<template>
  <div v-if="trip">
    <p>{{ trip.title }}</p>
    <p>{{ trip.description }}</p>
    <p>Du {{ trip.startDate }} au {{ trip.endDate }}</p>
  </div>
  <div v-else>
    <p>Chargement des infos du séjour</p>
  </div>
</template>

<style scoped>

</style>