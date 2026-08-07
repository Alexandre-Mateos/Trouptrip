<script lang="ts">
import { defineComponent } from 'vue';
import type { IInvitation } from "~/interfaces/participation/i-invitation";

export default defineComponent({
  name: "invitations",
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
  },
  async mounted() {
    try {
      await this.participationStore.fetchInvitations();
    } catch (e) {
      this.error = 'Impossible d\'afficher la liste des invitations pour le moment.';
    }
  }
})
</script>

<template>
  <template v-if="hasInvitations">
    <InvitationCard
        v-for="invitation in participationStore.invitationList"
        :key="invitation.id"
        :invitation="invitation"
        @decline="toggleDeclineInvitationModal(invitation)"
    />
  </template>

  <div v-else class="empty-state">
    <p>Rien à l'horizon pour l'instant !</p>
    <p>Et si c'était vous qui preniez les devants en organisant la prochaine escapade ?</p>
  </div>

  <BaseModal :open="isDeclineTripModalOpen">
    <div>
      <p>Vous êtes sur le point de décliner l'invitation de {{ activeInvitation?.invitedBy.firstname }} pour le voyage suivant :</p>
      <p class="font-bold">{{ activeInvitation?.trip.title }}</p>
      <p>Êtes-vous sûr de vouloir continuer ?</p>

      <div class="flex flex-row justify-center gap-2 mt-4">
        <DeleteButton icon="mdi:ban" label="Décliner" @click="declineInvitation" />
        <CancelButton @click="toggleDeclineInvitationModal" />
      </div>
    </div>
  </BaseModal>
</template>

<style scoped>

</style>