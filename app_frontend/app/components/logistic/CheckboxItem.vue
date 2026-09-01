<script lang="ts">
import { defineComponent, type PropType } from 'vue'
import type { IAssignedItem } from "~/interfaces/i-assignedItem";
import type { IPersonalItem } from "~/interfaces/personalItem/I-presonalItem";

export default defineComponent({
  name: "CheckboxItem",
  props: {
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
    async updateIsPacked() {
      const body = { isPacked: this.isPacked };

      if (!this.isFromAssignedGroupItem) {
        return usePersonalItemsStore().updatePersonalItem(this.item.id, body);
      }

      const currentUser = useUserStore().user;
      const assignmentsStore = useAssignmentsStore();
      const groupItem = useGroupItemsStore().groupItems.get(this.item.id);

      if (!currentUser || !groupItem) return;

      const assignment = assignmentsStore.getAssignmentByGroupItemAndUser(groupItem, currentUser.id);

      if (assignment) {
        await assignmentsStore.updateAssignment(groupItem, assignment.id, body);
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