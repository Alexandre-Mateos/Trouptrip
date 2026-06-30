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
  data() {
    return {
      isModalOpen: false,
      error: '',
    }
  },
  methods: {
    openModal() {
      this.isModalOpen = true;
    },
    closeModal() {
      this.isModalOpen = false;
    },
    async deleteTrip() {
      this.error = '';

      try {
        await useTripsStore().deleteTrip(this.trip.id);

        if (useDisplayStore().isDesktop && useTripsStore().getFirstTripId) {
          navigateTo({name: 'trips-id', params: {id: useTripsStore().getFirstTripId}});
        }
      } catch (error: any) {
        this.error = "Une erreur est survenue. Veuillez réessayer plus tard.";
      }
    }
  }
})
</script>

<template>

  <BaseButton @click="openModal">
    <Icon name="lucide:edit"></Icon>
    Modifier
  </BaseButton>
  <BaseButton @click="deleteTrip">
    <Icon name="material-symbols:delete-outline"></Icon>
    Supprimer
  </BaseButton>

  <div v-if="error" class="text-red-700 p-3 rounded mb-4">
    {{ error }}
  </div>

  <p>{{ trip.title }}</p>
  <p>{{ trip.description }}</p>
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

  <BaseModal :open="isModalOpen">
    <TripForm
        v-if="isModalOpen"
        :tripToEdit="trip"
        @done="closeModal"
    />
  </BaseModal>

</template>

<style scoped>

</style>