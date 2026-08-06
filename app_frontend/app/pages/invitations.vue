<script lang="ts">
import {defineComponent} from 'vue'
import InvitationCard from "~/components/InvitationCard.vue";

export default defineComponent({
  name: "invitations",
  components: {InvitationCard},
  data() {
    return {
      error: '',
    }
  },
  computed: {
    invitations() {
      return useParticipationStore().invitationList;
    }
  },
  async mounted() {
    try {
      await useParticipationStore().fetchInvitations();
    } catch (e) {
      this.error = 'impossible d\'afficher la lise des invitations pour le moment';
    }
  }
})
</script>

<template>
  <InvitationCard v-for="invitation in invitations" :key="invitation.id" :invitation="invitation"></InvitationCard>
</template>

<style scoped>

</style>