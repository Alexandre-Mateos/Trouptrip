<script lang="ts">
import {defineComponent} from 'vue'
import type {IInvitation} from "~/interfaces/participation/i-invitation";

export default defineComponent({
  name: "InvitationCard",
  props: {
    invitation: {
      type: Object as PropType<IInvitation>,
      required: true
    }
  },
  data(){
    return{
      error : ''
    }
  },
  methods:{
    async handleAccept() {
      console.log('click');
      const participationStore = useParticipationStore();
      const tripStore = useTripsStore();
      try{
        await participationStore.acceptInvitation(this.invitation.id);
        participationStore.removeInvitation(this.invitation.id);
        await tripStore.fetchTrip(this.invitation.trip.id);
      } catch(e){
        this.error = 'Impossible d\'accepter l\'invitation pour le moment';
      }
    },
  }
})
</script>

<template>
    <div class="bg-white shadow-md border border-solid border-trouptrip-accent-500 rounded-lg p-2 md:flex md:flex-row md:justify-between items-center">
      <div>
        <p>{{ invitation.invitedBy.firstname }} {{ invitation.invitedBy.lastname }} vous invite à participer au séjour :</p>
        <p>
          {{ invitation.trip.title }}
        </p>
        <p>Du {{ getFormatedDate(invitation.trip.startDate) }} au {{ getFormatedDate(invitation.trip.endDate) }}</p>
        <p v-if="error">{{error}}</p>
      </div>
      <div class="flex flex-row gap-2 justify-center">
        <ActionButton icon="tabler:circle-check" @click="handleAccept">Rejoindre</ActionButton>
        <DeleteButton icon="mdi:ban">Décliner</DeleteButton>
      </div>
    </div>
</template>

<style scoped>

</style>