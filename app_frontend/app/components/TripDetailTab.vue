<script lang="ts">
import { defineComponent, type PropType } from 'vue'
import type { IMapTripDetails } from "~/interfaces/trip/store/i-mapTripDetails";
import { participationStatus } from "~/utils/participationStatus";
import type { IParticipantList } from "~/interfaces/i-participantList";
import type { IMapParticipation } from "~/interfaces/participation/i-mapParticipation";
import ActionButton from "~/components/button/ActionButton.vue";
import DeleteButton from "~/components/button/DeleteButton.vue";
import SecondaryButton from "~/components/button/SecondaryButton.vue";
import EditButton from "~/components/button/EditButton.vue";
import CancelButton from "~/components/button/CancelButton.vue";
import TripForm from "~/components/form/TripForm.vue";
import InviteUserForm from "~/components/form/InviteUserForm.vue";

export default defineComponent({
  name: "TripDetailTab",
  components: {InviteUserForm, TripForm, CancelButton, EditButton, SecondaryButton, DeleteButton, ActionButton},
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
      isInviteUserModalOpen: false,
      isExcludeParticipantModalOpen: false,
      participantToExclude: null as IParticipantList | null,
      isExitTripModalOpen: false
    }
  },
  methods: {
    toggleEditTripModal() {
      this.isEditTripModalOpen = !this.isEditTripModalOpen;
    },
    toggleDeleteTripModal() {
      this.isDeleteTripModalOpen = !this.isDeleteTripModalOpen;
    },
    async deleteTrip() {
      this.error = '';

      try {
        await useTripsStore().deleteTrip(this.trip.id);

        if (useDisplayStore().isDesktop && useTripsStore().getFirstTripId) {
          navigateTo({ name: 'trips-id', params: { id: useTripsStore().getFirstTripId } });
        }
      } catch (error: any) {
        this.error = "Une erreur est survenue. Veuillez réessayer plus tard.";
      }
      this.toggleDeleteTripModal();
    },
    toggleInviteUserModal() {
      this.isInviteUserModalOpen = !this.isInviteUserModalOpen;
    },
    toggleExcludeParticipantModal(participant?: IParticipantList) {
      if (!this.isExcludeParticipantModalOpen && participant) {
        this.participantToExclude = participant;
      }

      this.isExcludeParticipantModalOpen = !this.isExcludeParticipantModalOpen;
    },
    async excludeParticipant() {
      try {
        if (this.participantToExclude) {
          await useParticipationStore().excludeParticipant(this.participantToExclude.participationId);
          this.toggleExcludeParticipantModal(this.participantToExclude);
        }
      } catch (e) {
      }
    },
    toggleExitTripModal() {
      this.isExitTripModalOpen = !this.isExitTripModalOpen;
    },
    async exitTrip() {
      if (!this.currentUserParticipation) return;

      const participationStore = useParticipationStore();
      const tripsStore = useTripsStore();
      const displayStore = useDisplayStore();

      try {
        await participationStore.exitTrip(this.currentUserParticipation.id);
        this.toggleExitTripModal();
        tripsStore.removeTripFromState(this.trip.id);
        const firstTripId = tripsStore.getFirstTripId;

        if (displayStore.isDesktop && firstTripId) {
          await navigateTo({ name: 'trips-id', params: { id: firstTripId } });
        } else {
          await navigateTo({ name: 'trips' });
        }

      } catch (error: any) {
        this.error = "Impossible de quitter le voyage pour le moment.";
      }
    }
  },
  computed: {
    isTripOwner(): boolean {
      const currentUserId = useUserStore().user?.id;
      const ownerId = this.trip?.owner?.id;

      return currentUserId === ownerId;
    },
    participants() {
      return useParticipationStore().getParticipantsGroupedByStatusByTripId(this.trip.id);
    },
    acceptedParticipants() {
      return this.participants.ACCEPTED ?? [];
    },
    otherSections() {
      return [
        { key: participationStatus.pending, title: 'En attente de confirmation' },
        { key: participationStatus.excluded, title: 'Exclus' },
        { key: participationStatus.left, title: 'Ont quitté le voyage' },
        { key: participationStatus.declined, title: 'Refusés' },
      ].map(section => ({
        ...section,
        list: this.participants[section.key] ?? []
      })).filter(section => section.list.length > 0);
    },
    currentUserParticipation(): IMapParticipation | undefined {
      return useParticipationStore().getParticipationByTripAndUser(this.trip.id);
    }
  },
})
</script>

<template>
  <div v-if="error" class="text-red-700 p-3 rounded mb-4">
    {{ error }}
  </div>

  <div class="flex flex-col gap-3">

    <section aria-label="description du séjour" class="flex flex-col gap-1">
      <p v-if="trip.description">{{ trip.description }}</p>
      <p>Du {{ getFormatedDate(trip.startDate) }} au {{ getFormatedDate(trip.endDate) }}</p>
    </section>

    <section
        aria-label="Rôle et actions"
        class="p-4 bg-surface-primary-trouptrip border border-trouptrip-accent-200 rounded-md flex flex-col gap-1"
    >
      <template v-if="isTripOwner">
        <p class="font-semibold">Vous êtes l'organisateur de ce séjour</p>
        <p class="text-sm">
          Vous pouvez inviter des participants ou gérer l'accès à ce séjour.
        </p>
        <ActionButton
            @click="toggleInviteUserModal"
            icon="fa6-solid:circle-plus"
        >
          Inviter un participant
        </ActionButton>
      </template>

      <template v-else>
        <p class="font-semibold">Séjour organisé par : {{ trip?.owner?.firstname }} {{ trip?.owner?.lastname }} </p>
        <p>En tant que participant vous pouvez quitter ce séjour à tout moment</p>
        <DeleteButton
            icon="mingcute:exit-line"
            @click="toggleExitTripModal"
        >
          Quitter le séjour
        </DeleteButton>
      </template>
    </section>

    <BorderTitleCard title="Participants confirmés">
      <template v-if="acceptedParticipants.length > 0">
        <ParticipantCard
            v-for="participant in acceptedParticipants"
            :key="participant.participationId"
            :participant="participant"
            :trip="trip"
            @exclude="toggleExcludeParticipantModal(participant)"
        />
      </template>

      <p v-else class="text-gray-600">
        Invitez quelques amis en cliquant sur le bouton juste au-dessus
      </p>
    </BorderTitleCard>

    <div v-if="otherSections.length > 0" aria-label="Autres participants">
      <Collapsible>
        <BorderTitleCard
            v-for="section in otherSections"
            :key="section.key"
            :title="section.title"
        >
          <ParticipantCard
              v-for="participant in section.list"
              :key="participant.participationId"
              :participant="participant"
              :trip="trip"
              @exclude="toggleExcludeParticipantModal(participant)"
          />
        </BorderTitleCard>
      </Collapsible>
    </div>

    <div
        v-if="isTripOwner"
        class="flex justify-end"
        role="group"
        aria-label="Actions de gestion du séjour"
    >
      <div class="flex flex-row gap-2">
        <EditButton
            @click="toggleEditTripModal"
            :aria-label="`Modifier les informations du séjour ${trip.title}`"
        >Modifier</EditButton>
        <DeleteButton
            @click="toggleDeleteTripModal"
            :aria-label="`Supprimer définitivement le séjour ${trip.title}`"
        >Supprimer</DeleteButton>
      </div>
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
    <div class="space-y-4">
      <p class="font-medium">Vous êtes sur le point de supprimer le voyage suivant :</p>
      <p class="text-lg font-bold text-center">{{ trip.title }}</p>
      <p class="text-sm text-gray-600">Cette action est irréversible : êtes-vous sûr de vouloir continuer ?</p>

      <div class="flex flex-row justify-center gap-2">
        <DeleteButton
            @click="deleteTrip"
            aria-label="Confirmer la suppression du séjour"
        >Supprimer</DeleteButton>
        <CancelButton
            @click="toggleDeleteTripModal"
            aria-label="Annuler la suppression et fermer la fenêtre"
        >Annuler</CancelButton>
      </div>
    </div>
  </BaseModal>

  <BaseModal :open="isInviteUserModalOpen">
    <InviteUserForm @done="toggleInviteUserModal" :trip="trip"/>
  </BaseModal>

  <BaseModal :open="isExcludeParticipantModalOpen">
    <div v-if="participantToExclude">
      <p>Vous êtes sur le point d'exclure : {{ participantToExclude.firstname }} {{ participantToExclude.lastname }}</p>
      <p>Cette action est irréversible: Êtes-vous sûr de vouloir continuer ?</p>

      <div class="flex flex-row justify-center gap-2">
        <DeleteButton
            @click="excludeParticipant"
            :aria-label="`Confirmer l'exclusion de ${participantToExclude.firstname} ${participantToExclude.lastname}`"
        >Exclure</DeleteButton>
        <CancelButton
            @click="toggleExcludeParticipantModal"
            aria-label="Annuler l'exclusion et fermer la fenêtre"
        >Annuler</CancelButton>
      </div>
    </div>
  </BaseModal>

  <BaseModal :open="isExitTripModalOpen">
    <div class="space-y-4">
      <p class="font-medium">Vous êtes sur le point de quitter le voyage suivant :</p>
      <p class="text-lg font-bold text-center">{{ trip.title }}</p>
      <p class="text-sm text-gray-600">Cette action est irréversible : êtes-vous sûr de vouloir continuer ?</p>

      <div class="flex flex-row justify-center gap-2">
        <DeleteButton
            icon="mingcute:exit-line"
            @click="exitTrip"
            aria-label="Confirmer le départ du séjour"
        >
          Quitter
        </DeleteButton>
        <CancelButton
            icon="tabler:arrow-back"
            @click="toggleExitTripModal"
            aria-label="Annuler et rester dans le séjour"
        >Annuler</CancelButton>
      </div>
    </div>
  </BaseModal>

</template>

<style scoped>
</style>