<script lang="ts">
import { defineComponent, type PropType } from 'vue'
import type { IAssignedItem } from "~/interfaces/i-assignedItem";
import type { IPersonalItem } from "~/interfaces/personalItem/I-presonalItem";
import type { IMapTripDetails } from "~/interfaces/trip/store/i-mapTripDetails";

export default defineComponent({
  name: "CheckboxItem",
  props: {
    trip: {
      type: Object as PropType<IMapTripDetails>,
      required: true
    },
    item: {
      type: Object as PropType<IAssignedItem | IPersonalItem>,
      required: true
    },
    isFromAssignedGroupItem: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      isPacked: this.item.isPacked,
    }
  },
  methods: {
    updateIsPacked() {
      const body = { isPacked: this.isPacked };

      if (this.isFromAssignedGroupItem) {
        const currentUser = useUserStore().user;
        if (currentUser) {
          const groupItem = useGroupItemsStore().groupItems.get(this.item.id);
          if (groupItem) {
            const assignment = useAssignmentsStore().getAssignmentByGroupItemAndUser(groupItem, currentUser.id);
            if (assignment) {
              useAssignmentsStore().updateAssignment(groupItem, assignment.id, body);
            }
          }
        }
      } else {
        usePersonalItemsStore().updatePersonalItem(this.trip, this.item.id, body);
      }
    }
  }
})
</script>

<template>
  <div class="flex items-center gap-2">
    <input
        type="checkbox"
        :id="`item-${item.id}`"
        :name="`item-${item.id}`"
        v-model="isPacked"
        @change="updateIsPacked"
        class="h-4 w-4 cursor-pointer"
    />
    <label :for="`item-${item.id}`"
           class="text-sm font-medium text-slate-700 cursor-pointer select-none transition-all"
           :class="{ 'line-through': isPacked }"
    >
            {{ item.name }}: {{ item.quantity }} {{ item.unit }}
    </label>
  </div>
</template>