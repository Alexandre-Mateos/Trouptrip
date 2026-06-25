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
  methods: {
    openModal() {
      const modal = this.$refs.editTripModal as any;
      if (modal) {
        modal.open();
      }
    },
    closeModal() {
      const modal = this.$refs.editTripModal as any;
      if (modal) {
        modal.close();
      }
    },
  }
})
</script>

<template>

  <BaseButton @click="openModal">
    <Icon name="lucide:edit" ></Icon>
    Modifier
  </BaseButton>

  <p>{{trip.title}}</p>
  <p>{{trip.description}}</p>
  <p>Du {{ getFormatedDate(trip.startDate) }} au {{ getFormatedDate(trip.endDate) }}</p>

  <div v-if="trip.participations && trip.participations.length > 0">
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
  </div>
  <div v-else>
    <p>Invitez quelques amis pour ce séjour</p>
  </div>

  <BaseModal ref="editTripModal">
    <TripForm @done="closeModal" :tripToEdit="trip"/>
  </BaseModal>

</template>

<style scoped>

</style>