<script lang="ts">
import {defineComponent} from 'vue'
import {useTripsStore} from "~/stores/trips";
import {useGroupItemsStore} from "~/stores/groupItems";
import {useAssignmentsStore} from "~/stores/assignments";
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";
import {usePersonalItemsStore} from "~/stores/personalItems";

export default defineComponent({
  name: "[id]",
  data() {
    return {
      id: null as number | null,
      hasError: false,
      tabs: [
        {label: "Mon séjour", slot: "myTrip"},
        {label: "La valise", slot: "theSuitCase"}
      ]
    }
  },
  async mounted() {
    const id = Number(this.$route.params.id);

    try {
      await useTripsStore().fetchTrip(id);
      await useGroupItemsStore().fetchGroupItems(id);
      await useAssignmentsStore().fetchAssignments(id);
      await usePersonalItemsStore().fetchPersonalItems(id);
    } catch {
      this.hasError = true;
    }
  },
  computed: {
    trip() {
      const id = Number(this.$route.params.id);
      return useTripsStore().getTripById(id) as IMapTripDetails | undefined;
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
            label: 'text-base cursor-pointer'
          }"
      >
        <template #myTrip>
          <TripDetailTab :trip="trip"></TripDetailTab>
        </template>
        <template #theSuitCase>
          <GroupItemTab :trip="trip"></GroupItemTab>
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