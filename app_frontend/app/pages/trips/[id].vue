<script lang="ts">
import {defineComponent} from 'vue'
import {useTripsStore} from "~/stores/trips";
import {useParticipationStore} from "~/stores/participation";
import {useGroupItemsStore} from "~/stores/groupItems";
import {useAssignmentsStore} from "~/stores/assignments";
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";
import {usePersonalItemsStore} from "~/stores/personalItems";

export default defineComponent({
  name: "[id]",
  emits: ['close'],
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
      await useParticipationStore().fetchParticipations(id);
      await useGroupItemsStore().fetchGroupItems(id);
      await useAssignmentsStore().fetchAssignments(id);
      await usePersonalItemsStore().fetchPersonalItems(id);

      if (this.$route.query.focus === 'true') {
        this.$nextTick(() => {
          (this.$el as HTMLElement)?.focus();
        });
      }

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
  <div tabindex="-1" @keydown.esc="$emit('close')">

    <div v-if="hasError" class="p-4 text-center text-red-500">
      <p>Impossible de charger les détails de ce séjour.</p>
      <p class="text-sm text-gray-400 mt-1">Ce voyage n'existe plus ou vous n'y avez pas accès.</p>
    </div>

    <div v-else-if="trip" class="w-full">
      <UCard
          :ui="{
              root: 'bg-white shadow-lg border-none ring-0',
              body: 'divide-none'
            }"
      >
        <h3 class="text-2xl text-center mb-4">{{trip.title}}</h3>
        <UTabs
            :items="tabs"
            :ui="{
      list: 'bg-surface-primary-trouptrip p-1 rounded-md',
      indicator: 'bg-trouptrip-accent-500 rounded-lg transition-all duration-300 ease-in-out',
      trigger: 'cursor-pointer data-[state=inactive]:text-trouptrip-title hover:data-[state=inactive]:not-disabled:text-trouptrip-title data-[state=active]:text-white',
      label: 'text-base'
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

  </div>
</template>
<style scoped>
</style>