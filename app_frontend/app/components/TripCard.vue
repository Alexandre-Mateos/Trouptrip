<script lang="ts">
import {defineComponent} from 'vue'
import 'temporal-polyfill/global';
export default defineComponent({
  name: "TripCard",
  props: {
    trip: {
      type: Object,
      required: true
    }
  },
})
</script>

<template>
  <UCard
      :ui="{
    root: 'bg-white shadow-md transform hover:translate-x-4 transition-all duration-200 border border-solid border-trouptrip-accent-500 ring-0',
    body: 'divide-none'
  }"
  >
    <p class="text-trouptrip-title text-base">{{ trip.title }}</p>
    <p class="text-trouptrip-neutral-500 text-sm">Du {{ getFormatedDate(trip.startDate) }} au {{ getFormatedDate(trip.endDate) }}</p>
  </UCard>
</template>

<style scoped>

</style>