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
    handledQty(groupItem: IMapGroupItem) {
      return useAssignmentsStore().getHandledQtyByGroupItem(groupItem)
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
            <div class="flex flex-1 justify-between items-center px-2">
              <div class="flex gap-2 items-center">

                <Icon
                    v-if="handledQty(groupItem) === groupItem.totalQuantity"
                    name="tabler:circle-check"
                    class="text-trouptrip-success-500 text-xl fill-trouptrip-success-500"
                ></Icon>
                <Icon
                    v-else-if="handledQty(groupItem) >0 && handledQty(groupItem) < groupItem.totalQuantity"
                    name="mdi-light:minus-circle"
                    class="text-trouptrip-accent-500 text-xl"
                ></Icon>
                <Icon
                    v-else
                    name="system-uicons:cross-circle"
                    class="text-trouptrip-neutral-500 text-xl"
                ></Icon>


                <span>
                {{ groupItem.name }}
                </span>
              </div>
              <div class="flex gap-1 items-center">
                <span>
                  {{ handledQty(groupItem) }}
                </span>
                <span class="text-trouptrip-neutral-400">
                  /{{ groupItem.totalQuantity }}
                </span>
                <span class="text-xs">
                  {{ groupItem.unit }}
                </span>
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