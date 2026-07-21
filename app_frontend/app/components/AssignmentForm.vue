<script lang="ts">
import {defineComponent} from 'vue'
import type {IMapGroupItem} from "~/interfaces/groupItem/i-mapGroupItem";

export default defineComponent({
  name: "AssignmentForm",
  props: {
    groupItem: {
      type: Object as PropType<IMapGroupItem>,
      required: true
    }
  },
  data() {
    return {
      assignedQty: 0,
      isRemoval: false,
      isSubmitting: false,
      errors: {} as Record<string, string[]>,
    }
  },
  methods: {
    async handleSubmit() {
      this.errors = {};
      this.isSubmitting = true;

      const body = {
        assignedQuantity: this.assignedQty,
        groupItem: this.groupItem['@id'],
        isRemoval: this.isRemoval
      };

      const userStore = useUserStore();
      if (!userStore.user) {
        return;
      }
      const currentUserId = userStore.user.id;

      try {
        for (const assignmentId of this.groupItem.assignments) {
          const assignment = useAssignmentsStore().assignments.get(assignmentId);

          if (assignment && assignment.assignedTo.id === currentUserId) {
            await useAssignmentsStore().updateAssignment(this.groupItem, assignment.id, body);
            this.assignedQty = 0;
            return;
          }
        }

        await useAssignmentsStore().submitAssignment(this.groupItem, body);
        this.assignedQty = 0;

      } catch (errors) {
        this.errors = useApiErrors().formatErrors(errors);
      } finally {
        this.isSubmitting = false;
      }
    }
  }
})
</script>

<template>
    <form @submit.prevent="handleSubmit" class="flex gap-1 items-end bg-surface-primary-trouptrip  p-1 rounded-md inset-shadow-sm">
      <NumberInput
          label="Tu ramènes quoi ?"
          min="0"
          :max="useAssignmentsStore().getRemainingQtyByGroupItem(groupItem)"
          v-model="assignedQty"
          class="flex-1"
      />
      <ActionButton type="submit" :disabled="isSubmitting" label="Ajouter" icon="raphael:arrowup" @click="isRemoval = false"></ActionButton>
      <ActionButton type="submit" :disabled="isSubmitting" label="Retirer" icon="raphael:arrowdown" color="var(--color-trouptrip-accent-500)" @click="isRemoval = true"></ActionButton>
    </form>
</template>

<style scoped>

</style>