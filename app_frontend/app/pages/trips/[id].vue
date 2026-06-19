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
      navTab: [
        {key: 'my-trip', label: 'Mon séjour'},
        {key: 'activities', label: 'Mes activités'},
        {key: 'tasks', label: 'Mes tâches'}
      ],
      activeTabKey: 'my-trip'
    }
  },
  methods: {
    activateTab(key: string) {
      this.activeTabKey = key;
    },
    isActiveTab(tabId: string) {
      return this.activeTabKey === tabId;
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
  <div>
    <div class="flex gap-2 justify-around">
      <template v-for="tab in navTab" :key="tab.key">
        <BaseTab @click="activateTab(tab.key)">{{ tab.label }}</BaseTab>
      </template>
    </div>

    <div>
      <div id="my-trip" :class="{ 'active-tab': isActiveTab('my-trip'), 'inactive-tab': !isActiveTab('my-trip') }">
        <TripDetailTab></TripDetailTab>
      </div>

      <div id="activities"
           :class="{ 'active-tab': isActiveTab('activities'), 'inactive-tab': !isActiveTab('activities') }">
        <TripActivityTab></TripActivityTab>
      </div>

      <div id="tasks" :class="{ 'active-tab': isActiveTab('tasks'), 'inactive-tab': !isActiveTab('tasks') }">
        <TripTaskTab></TripTaskTab>
      </div>
    </div>
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