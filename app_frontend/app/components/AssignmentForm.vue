<script lang="ts">
import {defineComponent} from 'vue'
import type {IMapGroupItem} from "~/interfaces/groupItem/i-mapGroupItem";
import ActionButton from "~/components/button/ActionButton.vue";

export default defineComponent({
  name: "AssignmentForm",
  components: {ActionButton},
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

      const userStore = useUserStore();
      if (!userStore.user) {
        return;
      }
      const currentUserId = userStore.user.id;

      try {
        for (const assignmentId of this.groupItem.assignments) {
          const assignment = useAssignmentsStore().assignments.get(assignmentId);

          if (assignment && assignment.assignedTo.id === currentUserId) {

            const bodyForUpdate = {
              assignedQuantity: this.assignedQty,
              isRemoval:this.isRemoval
            };

            await useAssignmentsStore().updateAssignment(this.groupItem, assignment.id, bodyForUpdate);
            this.assignedQty = 0;
            return;
          }
        }

        const bodyForPost = {
          assignedQuantity: this.assignedQty,
          groupItem: this.groupItem['@id']
        };
        await useAssignmentsStore().submitAssignment(this.groupItem, bodyForPost);
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
  <form
      @submit.prevent="handleSubmit"
      class="flex flex-col lg:flex-row gap-1 items-center lg:items-end bg-trouptrip-accent-100 p-2 rounded-md inset-shadow-sm"
      :aria-label="`Formulaire de contribution pour ${groupItem.name}`"
  >
    <NumberInput
        label="Tu ramènes quoi ?"
        min="0"
        :max="groupItem.totalQuantity"
        v-model="assignedQty"
        class="flex-1"
        :errors="errors?.assignedQuantity"
        :id="`${groupItem.id}-${groupItem.name}`"
    />

    <div class="flex gap-1" role="group" aria-label="Actions de gestion des quantités">
      <ActionButton
          type="submit"
          :disabled="isSubmitting"
          icon="raphael:arrowup"
          @click="isRemoval = false"
          :aria-label="`Ajouter ${assignedQty} ${groupItem.unit || ''} à ${groupItem.name}`"
      >Ajouter</ActionButton>
      <ActionButton
          type="submit"
          :disabled="isSubmitting"
          icon="raphael:arrowdown"
          color="var(--color-trouptrip-accent-500)"
          @click="isRemoval = true"
          :aria-label="`Retirer ${assignedQty} ${groupItem.unit || ''} de ${groupItem.name}`"
      >Retirer</ActionButton>
    </div>
  </form>
</template>

<style scoped>
</style>