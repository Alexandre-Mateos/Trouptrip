<script lang="ts">
import {defineComponent} from 'vue'
import type {PropType} from 'vue'
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails"
import type {IMapGroupItem} from "~/interfaces/groupItem/i-mapGroupItem";

export default defineComponent({
  name: 'GroupItemTab',
  props: {
    trip: {
      type: Object as PropType<IMapTripDetails>,
      required: true
    }
  },
  methods: {
    remainingQty(groupItem: IMapGroupItem) {
      return useAssignmentsStore().getRemainingQtyByGroupItem(groupItem)
    },
    assignments(groupItem: IMapGroupItem) {
      return useAssignmentsStore().getAssignmentsByGroupItem(groupItem)
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
        <CollapsibleCard>
          <template #header>
            <div class="flex flex-1 justify-between px-2">
              {{ groupItem.name }}
              <div>
                {{ remainingQty(groupItem) }}
                {{ groupItem.unit }}
              </div>
            </div>
          </template>
          <template #body>
            <template v-for="assignment in assignments(groupItem)" :key="assignment.id">
              <AssignmentLabel :assignment="assignment"></AssignmentLabel>
            </template>
          </template>
        </CollapsibleCard>
      </template>
    </div>
  </Collapsible>
</template>

<style scoped>

</style>