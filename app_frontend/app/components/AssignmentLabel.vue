<script lang="ts">
import {defineComponent, type PropType} from 'vue'
import type {IDetailedAssignment} from "~/interfaces/assignment/i-detailedAssignment";

export default defineComponent({
  name: "AssignmentLabel",
  props: {
    assignment: {
      type: Object as PropType<IDetailedAssignment>,
      required: true
    }
  }
})
</script>

<template>
  <div class="flex gap-2 text-xs">
    <div class="flex gap-1">
      <p>{{assignment.assignedTo.firstname}}</p>
      <p>{{assignment.assignedTo.lastname}}</p>
    </div>
    <div>
      <p>x{{assignment.assignedQuantity}}</p>
    </div>
  </div>
</template>

<style scoped>

</style>