<script lang="ts">
import {defineComponent} from 'vue'
import InvitationCard from "~/components/InvitationCard.vue";
import type {IInvitation} from "~/interfaces/participation/i-invitation";

export default defineComponent({
  name: "invitations",
  components: {InvitationCard},
  data() {
    return {
      error: '',
      isDeclineTripModalOpen: false,
      activeInvitation: null as IInvitation | null,
    }
  },
  methods: {
    toggleDeclineInvitationModal(invitation: IInvitation){
      if(!this.isDeclineTripModalOpen){
        this.activeInvitation = invitation;
      }
      this.isDeclineTripModalOpen = !this.isDeclineTripModalOpen;
    },
    declineInvitation(){
      if(!this.activeInvitation){
        return;
      }
      try{
        this.participationStore.declineInvitation(this.activeInvitation?.id);
      } catch {
        this.error = 'Une erreur est survenue merci de réessayer plus tard';
      }finally{
        this.participationStore.removeInvitation(this.activeInvitation?.id);
        this.toggleDeclineInvitationModal(this.activeInvitation);
      }
    }
  },
  computed: {
    participationStore(){
      return useParticipationStore()
    }
  },
  async mounted() {
    try {
      await this.participationStore.fetchInvitations();
    } catch (e) {
      this.error = 'impossible d\'afficher la lise des invitations pour le moment';
    }
  }
})
</script>

<template>
  <InvitationCard v-for="invitation in participationStore.invitationList" :key="invitation.id" :invitation="invitation" @decline="toggleDeclineInvitationModal(invitation)"></InvitationCard>

  <BaseModal :open="isDeclineTripModalOpen">
    <div>
      <p>Vous Etes sur le point de décliner l'invitation de {{ activeInvitation?.invitedBy.firstname }} pour le voyage suivant :</p>
      <p>{{ activeInvitation?.trip.title }}</p>
      <p>Etes vous sûr de vouloir continuer ?</p>
      <div class="flex flex-row justify-center gap-2">
        <DeleteButton icon="mdi:ban" label="Décliner" @click="declineInvitation"></DeleteButton>
        <CancelButton @click="toggleDeclineInvitationModal"></CancelButton>
      </div>
    </div>
  </BaseModal>

</template>

<style scoped>

</style>