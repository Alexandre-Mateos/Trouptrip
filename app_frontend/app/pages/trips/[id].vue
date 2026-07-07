<script lang="ts">
import {defineComponent} from 'vue'
import {useTripsStore} from "~/stores/trips";
import type {IMapTripDetails} from "~/interfaces/i-mapTripDetails";

export default defineComponent({
  name: "[id]",
  data() {
    return {
      id: null as number | null,
      hasError: false,
      tabs: [
        {label: "Mon séjour", slot: "myTrip" },
        {label: "Le coffre", slot: "theTrunk"},
        {label: "Sac à dos", slot: "myBackPack"  }
      ]
    }
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
    }
  }
})
</script>

<template>
  <div v-if="hasError" class="p-4 text-center text-red-500">
    <p>Impossible de charger les détails de ce séjour.</p>
    <p class="text-sm text-gray-400 mt-1">Ce voyage n'existe plus ou vous n'y avez pas accès.</p>
  </div>

  <div v-else-if="trip" class="w-full">
    <UCard
        :ui="{
              root: 'bg-white shadow-md border-none ring-0',
              body: 'divide-none'
            }"
    >
      <UTabs
          :items="tabs"
          :ui="{
            list: 'bg-surface-primary-trouptrip p-1 rounded-md',
            indicator: 'bg-trouptrip-accent-500 rounded-lg transition-all duration-300 ease-in-out',
            trigger: 'data-[state=inactive]:text-trouptrip-title data-[state=active]:text-white',
            label: 'text-base'
          }"
      >
        <template #myTrip>
            <TripDetailTab :trip="trip"></TripDetailTab>
        </template>
        <template #theTrunk>
          <p>A venir</p>
          <p>C'est ici que seront renseigné les items du groupes</p>
        </template>
        <template #myBackPack>
          <p>A venir</p>
          <p>C'est ici que seront renseigné les items personnels de l'utilisateur</p>
        </template>
      </UTabs>
    </UCard>
  </div>

  <div v-else class="p-4 text-center">
    <p>Chargement des données du voyage...</p>
  </div>
</template>
<style scoped>
</style>