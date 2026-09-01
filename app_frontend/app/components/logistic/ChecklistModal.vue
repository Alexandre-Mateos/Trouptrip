<script lang="ts">
import { defineComponent } from 'vue'
import type { PropType } from 'vue'

import type { IMapTripDetails } from '~/interfaces/trip/store/i-mapTripDetails'

import SecondaryButton from '~/components/button/SecondaryButton.vue'

export default defineComponent({
  name: 'ChecklistModal',

  components: {
    SecondaryButton
  },

  props: {
    open: {
      type: Boolean,
      required: true
    },

    trip: {
      type: Object as PropType<IMapTripDetails>,
      required: true
    }
  },

  emits: ['close'],

  computed: {
    currentUserId() {
      return useUserStore().user?.id
    },

    assignedGroupItems() {
      if (!this.currentUserId) {
        return []
      }

      return useAssignmentsStore()
          .getAssignedGroupItemsByUserAndTrip(
              this.trip.id,
              this.currentUserId
          )
    },

    personalItems() {
      return usePersonalItemsStore()
          .getPersonalItemsByTripId(this.trip.id)
    },

    totalPersonalItemsCount() {
      return usePersonalItemsStore()
          .getPersonalItemTotalCountByTripId(
              this.trip.id
          )
    },

    totalAssignedGroupItemsCount() {
      if (!this.currentUserId) {
        return 0
      }

      return useAssignmentsStore()
          .getAssignedGroupItemsTotalCountByUserAndTrip(
              this.trip.id,
              this.currentUserId
          )
    }
  }
})
</script>

<template>
  <BaseModal :open="open">
    <section
        aria-labelledby="checklist-title"
        class="flex flex-col gap-2"
    >
      <h4
          id="checklist-title"
          class="text-lg font-bold text-center"
      >
        Checklist de départ
      </h4>

      <ChecklistProgressBar :trip-id="trip.id" />

      <div
          v-if="totalAssignedGroupItemsCount > 0"
          class="
          border
          border-solid
          border-trouptrip-accent-500
          rounded-md
          p-2
        "
      >
        <CheckboxItem
            v-for="assignedItem in assignedGroupItems"
            :key="assignedItem.id"
            :item="assignedItem"
            :is-from-assigned-group-item="true"
        />
      </div>

      <div
          v-if="totalPersonalItemsCount > 0"
          class="
          border
          border-solid
          border-trouptrip-accent-500
          rounded-md
          p-2
        "
      >
        <CheckboxItem
            v-for="personalItem in personalItems"
            :key="personalItem.id"
            :item="personalItem"
            :trip="trip"
        />
      </div>

      <SecondaryButton
          class="m-auto"
          @click="$emit('close')"
      >
        Fermer
      </SecondaryButton>
    </section>
  </BaseModal>
</template>