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
  <div class="card bg-primary text-primary-content">
    <div class="card-body">
      <h2 class="card-title">{{trip.title}}</h2>
      <p>Du {{getFormatedDate(trip.startDate)}} au {{getFormatedDate(trip.endDate)}}</p>
    </div>
  </div>
</template>

<style scoped>

</style>