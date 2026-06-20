<script lang="ts">
import {defineComponent} from 'vue'
import type {IMapTripDetails} from "~/interfaces/i-mapTripDetails";

export default defineComponent({
  name: "TripDetailTab",
  props: {
    trip: {
      type: Object as PropType<IMapTripDetails>,
      required: true
    }
  },
})
</script>

<template>
  <p>{{trip.title}}</p>
  <p>{{trip.description}}</p>
  <p>Du {{ getFormatedDate(trip.startDate) }} au {{ getFormatedDate(trip.endDate) }}</p>
  <table>
    <thead>
    <tr>
      <th scope="col">Prénom</th>
      <th scope="col">Nom</th>
      <th scope="col">Statut</th>
    </tr>
    </thead>
    <tbody>
    <tr v-for="(participant, index) in trip.participations" :key="participant.id">
      <th scope="row">{{ participant.participant.firstname }}</th>
      <td>{{ participant.participant.lastname }}</td>
      <td>{{ participant.status }}</td>
    </tr>
    </tbody>
  </table>
</template>

<style scoped>

</style>