<script lang="ts">
import { defineComponent } from 'vue';
import type { IInvitation } from "~/interfaces/participation/i-invitation";
import DeleteButton from "~/components/button/DeleteButton.vue";
import CancelButton from "~/components/button/CancelButton.vue";

export default defineComponent({
  name: "invitations",
  components: {CancelButton, DeleteButton},
  setup() {
    useHead({
      title: 'Mes invitations',
      meta: [
        {
          name: 'description',
          content: 'Consultez et gérez vos invitations reçues pour rejoindre des voyages en groupe sur TroupTrip.'
        }
      ]
    })
  },
  data() {
    return {
      error: '',
      isDeclineTripModalOpen: false,
      activeInvitation: null as IInvitation | null,
    }
  },
  methods: {
    toggleDeclineInvitationModal(invitation?: IInvitation | null) {
      if (!this.isDeclineTripModalOpen && invitation) {
        this.activeInvitation = invitation;
      }
      this.isDeclineTripModalOpen = !this.isDeclineTripModalOpen;

      if (!this.isDeclineTripModalOpen) {
        this.activeInvitation = null;
      }
    },
    async declineInvitation() {
      if (!this.activeInvitation) {
        return;
      }

      const invitationId = this.activeInvitation.id;

      try {
        await this.participationStore.declineInvitation(invitationId);
        this.participationStore.removeInvitation(invitationId);
        this.toggleDeclineInvitationModal();
      } catch (e) {
        this.error = 'Une erreur est survenue, merci de réessayer plus tard.';
      }
    }
  },
  computed: {
    participationStore() {
      return useParticipationStore();
    },
    hasInvitations() {
      return this.participationStore.invitations.size > 0;
    }
  }
})
</script>

<template>
  <div class="flex flex-col gap-6 w-full py-2">
    <header class="text-center space-y-1">
      <h1 class="text-3xl font-bold">Mes invitations</h1>
    </header>

    <p v-if="error" role="alert" class="error-message text-red-800 font-medium text-center">
      {{ error }}
    </p>

    <section v-if="hasInvitations" class="flex flex-col gap-4" aria-label="Liste des invitations reçues">
      <InvitationCard
          v-for="invitation in participationStore.invitationList"
          :key="invitation.id"
          :invitation="invitation"
          @decline="toggleDeclineInvitationModal(invitation)"
      />
    </section>

    <section v-else class="p-6 bg-surface-primary-trouptrip border border-trouptrip-accent-200 rounded-md text-center space-y-2">
      <p class="font-medium text-lg">Rien à l'horizon pour l'instant !</p>
      <p class="text-sm text-gray-600">
        Et si c'était vous qui preniez les devants en organisant la prochaine escapade ?
      </p>
    </section>

    <BaseModal :open="isDeclineTripModalOpen">
      <div>
        <p>Vous êtes sur le point de décliner l'invitation de {{ activeInvitation?.invitedBy.firstname }} pour le voyage suivant :</p>
        <p class="font-bold my-2 text-center text-lg">{{ activeInvitation?.trip.title }}</p>
        <p>Êtes-vous sûr de vouloir continuer ?</p>

        <div class="flex flex-row justify-center gap-2 mt-4">
          <DeleteButton icon="mdi:ban" @click="declineInvitation" >Décliner</DeleteButton>
          <CancelButton @click="toggleDeclineInvitationModal">Annuler</CancelButton>
        </div>
      </div>
    </BaseModal>
  </div>
</template>

<style scoped>

</style>