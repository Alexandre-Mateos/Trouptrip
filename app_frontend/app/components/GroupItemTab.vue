<script lang="ts">
import { defineComponent } from 'vue'
import type { PropType } from 'vue'
import type { IMapTripDetails } from "~/interfaces/trip/store/i-mapTripDetails"

export default defineComponent({
  name: 'GroupItemTab',
  props: {
    trip: {
      type: Object as PropType<IMapTripDetails>,
      required: true
    }
  },
  computed: {
    groupItems() {
      return useGroupItemsStore().getGroupItemListByTrip(this.trip)
    }
  }
})
</script>

<template>
  <Collapsible>
    <div class="p-2 flex flex-col gap-2">
      <template v-for="groupItem in groupItems" :key="groupItem.id">
        <CollapsibleCard >
          <template #header>
            ceci est le header de la card {{ groupItem.id }}
          </template>
          <template #body>
            ceci est le body de la card {{ groupItem.id }}
          </template>
        </CollapsibleCard>
      </template>
    </div>
  </Collapsible>
</template>

<style scoped>

</style>