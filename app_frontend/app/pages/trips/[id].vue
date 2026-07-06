<script lang="ts">
import {defineComponent} from 'vue'
import {useTripsStore} from "~/stores/trips";
import type {IMapTripDetails} from "~/interfaces/i-mapTripDetails";

export default defineComponent({
  name: "[id]",
  data() {
    return {
      id: null as number | null,
      navTab: [
        {key: 'my-trip', label: 'Mon séjour'}
      ],
      activeTabKey: 'my-trip',
      hasError: false
    }
  },
  methods: {
  },
  async mounted() {
    const tripId = useRoute().params.id;
    if (tripId) {
      this.id = Number(tripId);
      try {
        await useTripsStore().fetchTrip(this.id);
      } catch (error) {
        this.hasError = true;
      }
    }
  },
  computed: {
    trip() {
      if (this.id === null) return null;
      return useTripsStore().getTripById(this.id) as IMapTripDetails | undefined;
    },
    displayStore(){
      return useDisplayStore();
    }
  }
})
</script>

<template>
  <div v-if="hasError" class="p-4 text-center text-red-500">
    <p>Impossible de charger les détails de ce séjour.</p>
    <p class="text-sm text-gray-400 mt-1">Ce voyage n'existe plus ou vous n'y avez pas accès.</p>
  </div>

  <div v-else-if="trip">
    <UCard
        :ui="{
          root: 'bg-white shadow-md border-none ring-0',
          body: 'divide-none'
        }">
      <div id="my-trip" :class="{ 'active-tab': displayStore.isActiveTab('my-trip'), 'inactive-tab': !displayStore.isActiveTab('my-trip') }">
        <TripDetailTab :trip="trip"></TripDetailTab>
      </div>
    </UCard>
  </div>

  <div v-else class="p-4 text-center">
    <p>Chargement des données du voyage...</p>
  </div>
</template>

<style scoped>
.active-tab {
  display: block;
}

.inactive-tab {
  display: none;
}
</style>