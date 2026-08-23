<script lang="ts">
import {defineComponent} from 'vue'
import type {IInvitation} from "~/interfaces/participation/i-invitation";
import ActionButton from "~/components/button/ActionButton.vue";
import DeleteButton from "~/components/button/DeleteButton.vue";

export default defineComponent({
  name: "InvitationCard",
  components: {DeleteButton, ActionButton},
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
    emits: ['decline'],
  methods:{
    async handleAccept() {
      const participationStore = useParticipationStore();
      const tripStore = useTripsStore();
      const invitationId = this.invitation.id;
      const tripId = this.invitation.trip.id;


      try{
        await participationStore.acceptInvitation(invitationId);
        participationStore.removeInvitation(invitationId);
        await tripStore.fetchTrip(tripId);

        await navigateTo({ name: 'trips-id', params: { id: tripId } });

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
        <DeleteButton icon="mdi:ban" @click="$emit('decline')">Décliner</DeleteButton>
      </div>
    </div>
</template>

<style scoped>

</style>