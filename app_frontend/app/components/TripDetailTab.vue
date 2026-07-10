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
      error: ''
    }
  },
  methods: {
    toggleEditTripModal() {
      this.isEditTripModalOpen = !this.isEditTripModalOpen;
    },
    toggleDeleteTripModal(){
      this.isDeleteTripModalOpen = !this.isDeleteTripModalOpen;
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
      this.toggleDeleteTripModal();
    }
  },
  computed: {
    isTripOwner(): boolean {
      const currentUserId = useUserStore().user?.id;
      const ownerId = this.trip?.owner?.id;

      return currentUserId === ownerId;
    },
    participant(){
      return useTripsStore().getParticipantListByTripId(this.trip.id)
    }
  },
})
</script>

<template>
  <div v-if="error" class="text-red-700 p-3 rounded mb-4">
    {{ error }}
  </div>

  <div class="flex flex-col gap-2">
    <p class="text-lg text-center">{{ trip.title }}</p>
    <p v-if="trip.description">{{ trip.description }}</p>
    <p>Du {{ getFormatedDate(trip.startDate) }} au {{ getFormatedDate(trip.endDate) }}</p>
  </div>

  <div v-if="trip.participations && trip.participations.length > 0" class="rounded-md p-4 inset-shadow-sm border border-trouptrip-accent-200">
    <p class="text-center">Participants du voyage </p>
      <UTable :data="participant" class="flex-1" />
  </div>
  <div v-else>
    <p>Invitez quelques amis pour ce séjour</p>
  </div>
  <div class="flex justify-end">
    <div v-if="isTripOwner" class="flex flex-row gap-2">
      <EditButton @click="toggleEditTripModal"></EditButton>
      <DeleteButton @click="toggleDeleteTripModal"></DeleteButton>
    </div>
  </div>

  <BaseModal :open="isEditTripModalOpen">
    <TripForm
        :open="isEditTripModalOpen"
        :tripToEdit="trip"
        @done="toggleEditTripModal"
    />
  </BaseModal>
  <BaseModal :open="isDeleteTripModalOpen">
    <div>
      <p>Vous Etes sur le point de supprimer le voyage suivant :</p>
      <p>{{trip.title}}</p>
      <p>Etes vous sûr de vouloir continuer ?</p>
      <div class="flex flex-row justify-center gap-2">
        <DeleteButton @click="deleteTrip"></DeleteButton>
        <CancelButton @click="toggleDeleteTripModal"></CancelButton>
      </div>
    </div>
  </BaseModal>

</template>

<style scoped>

</style>