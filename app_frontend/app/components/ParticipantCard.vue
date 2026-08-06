<script lang="ts">
import {defineComponent} from 'vue'
import type {IParticipantList} from "~/interfaces/i-participantList";
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";

export default defineComponent({
  name: "ParticipantCard",
  props: {
    participant:{
      type: Object as PropType<IParticipantList>,
      required: true
    },
    trip: {
      type: Object as PropType<IMapTripDetails>,
      required: true
    }
  },
  methods: {
    async excludeParticipant(){
      try{
        await useParticipationStore().excludeParticipant(this.participant.participationId);
      } catch(e){}
    }
  }
})
</script>

<template>
  <div class="flex flex-row justify-between">
    <div class="flex felx-row gap-2">
      <p>{{ participant.firstname }}</p>
      <p>{{ participant.lastname }}</p>
    </div>
    <div v-if="useUserStore().user?.id === trip.owner.id">
      <DeleteButton icon="mdi:ban" @click="excludeParticipant"></DeleteButton>
    </div>
  </div>
</template>

<style scoped>

</style>