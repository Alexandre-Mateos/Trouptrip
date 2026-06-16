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
  methods: {
    getFormatedDate(rawDate: string){
      const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

      const date = Temporal.Instant.from(rawDate)
          .toZonedDateTimeISO(userTimeZone)
          .toPlainDate();
      const dateFormatter = new Intl.DateTimeFormat('fr-FR', { dateStyle: 'long' });
      return dateFormatter.format(date);
    }
  }
})
</script>

<template>
  <div class="card bg-primary text-primary-content max-w-96">
    <div class="card-body">
      <h2 class="card-title">{{trip.title}}</h2>
      <p>Du {{getFormatedDate(trip.startDate)}} au {{getFormatedDate(trip.endDate)}}</p>
    </div>
  </div>
</template>

<style scoped>

</style>