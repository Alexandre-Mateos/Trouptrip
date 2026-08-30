<script lang="ts">
import { defineComponent } from 'vue'
import type { PropType } from 'vue'
import type { IMapTripDetails } from '~/interfaces/trip/store/i-mapTripDetails'

export default defineComponent({
  name: 'GroupItemTab',
  props: {
    trip: {
      type: Object as PropType<IMapTripDetails>,
      required: true
    }
  }
})
</script>

<template>
  <div class="flex flex-col gap-2">
    <GroupItemsSection :trip="trip" />
    <BackpackSection :trip="trip" />
  </div>
</template>