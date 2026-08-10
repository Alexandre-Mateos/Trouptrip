<script lang="ts">
import { defineComponent, type PropType } from 'vue'
import type { IParticipantList } from "~/interfaces/i-participantList";
import type { IMapTripDetails } from "~/interfaces/trip/store/i-mapTripDetails";
import { participationStatus } from "~/utils/participationStatus";

export default defineComponent({
  name: "ParticipantCard",
  props: {
    participant: {
      type: Object as PropType<IParticipantList>,
      required: true
    },
    trip: {
      type: Object as PropType<IMapTripDetails>,
      required: true
    }
  },
  emits: ['exclude'],
  computed: {
    canBeExcluded(): boolean {
      const isOwner = useUserStore().user?.id === this.trip.owner.id;
      return isOwner && (participationStatus.accepted === this.participant.status || participationStatus.pending === this.participant.status);
    }
  }
})
</script>

<template>
  <div class="flex flex-row justify-between items-center">
    <div class="flex flex-row gap-2">
      <p>{{ participant.firstname }}</p>
      <p>{{ participant.lastname }}</p>
    </div>

    <div v-if="canBeExcluded">
      <DeleteButton
          icon="mdi:ban"
          @click="$emit('exclude')"
          :aria-label="`Exclure ${participant.firstname} ${participant.lastname}`"
      />
    </div>
  </div>
</template>