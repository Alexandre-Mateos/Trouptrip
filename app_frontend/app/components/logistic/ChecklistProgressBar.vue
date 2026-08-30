<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "ChecklistProgressBar",
  props: {
    tripId: {
      type: Number,
      required: true
    }
  },
  computed: {
    currentUserId(){
      return useUserStore().user?.id;
    },
    handledItemsCount(){
      const handledPersonalItemsCount = usePersonalItemsStore().getIsPackedPersonalItemsCountByTripId(this.tripId);
      if(!this.currentUserId){
        return handledPersonalItemsCount;
      }
      return handledPersonalItemsCount + useAssignmentsStore().getIsPackedAssignedItemsCountByTripId(this.tripId, this.currentUserId);
    },
    maxItemCount(){
      const personalItemTotalCount = usePersonalItemsStore().getPersonalItemTotalCountByTripId(this.tripId);
      if(!this.currentUserId){
        return personalItemTotalCount;
      }
      return personalItemTotalCount + useAssignmentsStore().getAssignedGroupItemsTotalCountByUserAndTrip(this.tripId, this.currentUserId);
    }
  }
})
</script>

<template>
  <h2>Checklist complétée à :</h2>
  <UProgress v-model="handledItemsCount" :max="maxItemCount" status size="xl"/>
</template>

<style scoped>

</style>