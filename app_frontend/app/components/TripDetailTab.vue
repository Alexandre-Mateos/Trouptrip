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
      isEditTripModalOpen: false,
      isDeleteTripModalOpen: false,
      error: '',
    }
  },
  methods: {
    openEditTripModal() {
      this.isEditTripModalOpen = true;
    },
    closeEditTripModal() {
      this.isEditTripModalOpen = false;
    },
    openDeleteTripModal() {
      this.isDeleteTripModalOpen = true;
    },
    closeDeleteTripModal() {
      this.isDeleteTripModalOpen = false;
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
      this.closeDeleteTripModal();
    }
  },
  computed: {
    // On exclue le user connecté de la liste des participants à afficher
    otherParticipants(): any[] {
      if (!this.trip.participations) return [];

      const currentUserId = useUserStore().user?.id;
      return this.trip.participations.filter(
          participation => participation.participant.id !== currentUserId
      );
    },
    isTripOwner(): boolean {
      const currentUserId = useUserStore().user?.id;
      const ownerId = this.trip?.owner?.id;

      return currentUserId === ownerId;
    }
  },
})
</script>

<template>
  <div v-if="isTripOwner">
    <BaseButton @click="openEditTripModal">
      <Icon name="lucide:edit"></Icon>
      Modifier
    </BaseButton>
    <BaseButton @click="openDeleteTripModal">
      <Icon name="material-symbols:delete-outline"></Icon>
      Supprimer
    </BaseButton>
  </div>
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
      <tr v-for="participation in otherParticipants" :key="participation.id">
        <th scope="row">{{ participation.participant.firstname }}</th>
        <td>{{ participation.participant.lastname }}</td>
        <td>{{ participation.status }}</td>
      </tr>
      </tbody>
    </table>
  </div>
  <div v-else>
    <p>Invitez quelques amis pour ce séjour</p>
  </div>

  <BaseModal :open="isEditTripModalOpen">
    <TripForm
        :open="isEditTripModalOpen"
        :tripToEdit="trip"
        @done="closeEditTripModal"
    />
  </BaseModal>
  <BaseModal :open="isDeleteTripModalOpen">
    <div>
      <p>Vous Etes sur le point de supprimer ce voyage</p>
      <p>Etes vous sûr de vouloir continuer ?</p>
      <BaseButton @click="deleteTrip">Supprimer</BaseButton>
      <BaseButton @click="closeDeleteTripModal">Annuler</BaseButton>
    </div>
  </BaseModal>

</template>

<style scoped>

</style>