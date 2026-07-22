<script lang="ts">
import {defineComponent, type PropType} from 'vue'
import type {IDetailedAssignment} from "~/interfaces/assignment/i-detailedAssignment";
import type {IMapGroupItem} from "~/interfaces/groupItem/i-mapGroupItem";

export default defineComponent({
  name: "AssignmentLabel",
  props: {
    assignment: {
      type: Object as PropType<IDetailedAssignment>,
      required: true
    },
    groupItem: {
      type: Object as PropType<IMapGroupItem>,
      required: true
    }
  },
  methods: {
    deleteAssignment(){
      useAssignmentsStore().deleteAssignment(this.assignment.id, this.groupItem)
    }
  }
})
</script>

<template>
  <div class="w-fit flex gap-2 text-xs label bg-trouptrip-secondary-300 border border-trouptrip-secondary-600 p-1 rounded-md font-bold">
    <div class="flex gap-1">
      <span>{{assignment.assignedTo.firstname}}</span>
      <span>{{assignment.assignedTo.lastname}}</span>
    </div>
      <span>x{{assignment.assignedQuantity}}</span>
    <DeleteButton @click="deleteAssignment" v-if="assignment.assignedTo.id === useUserStore().user?.id"></DeleteButton>
  </div>
</template>

<style scoped>
</style>