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
      root: 'bg-white border border-solid border-trouptrip-accent-500 shadow-md transform hover:translate-x-3 transition-all duration-200',
    }"
  >
    <h2 class="text-trouptrip-title">{{ trip.title }}</h2>
    <p class="text-trouptrip-neutral-500 text-sm">Du {{ getFormatedDate(trip.startDate) }} au {{ getFormatedDate(trip.endDate) }}</p>
  </UCard>
</template>

<style scoped>

</style>