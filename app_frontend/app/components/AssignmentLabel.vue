<script lang="ts">
import {defineComponent, type PropType} from 'vue'
import type {IDetailedAssignment} from "~/interfaces/assignment/i-detailedAssignment";
import type {IMapGroupItem} from "~/interfaces/groupItem/i-mapGroupItem";
import DeleteButton from "~/components/button/DeleteButton.vue";
import CancelButton from "~/components/button/CancelButton.vue";

export default defineComponent({
  name: "AssignmentLabel",
  components: {CancelButton, DeleteButton},
  data() {
    return {
      isDeleteModalOpen: false,
    }
  },
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
    },
    toggleDeleteModal(){
      this.isDeleteModalOpen = !this.isDeleteModalOpen;
    }
  },
  computed: {
    currentUser(){
      return useUserStore().user
    }
  }
})
</script>

<template>
  <div class="w-fit flex gap-2 text-xs label bg-trouptrip-secondary-300 border border-trouptrip-secondary-600 p-1 rounded-md font-bold items-center">
    <div v-if="assignment.assignedTo.id === currentUser?.id">
      <span>Moi</span>
    </div>
    <div class="flex gap-1" v-else>
      <span>{{assignment.assignedTo.firstname}}</span>
      <span>{{assignment.assignedTo.lastname}}</span>
    </div>
      <span>x{{assignment.assignedQuantity}}</span>
    <DeleteButton @click="toggleDeleteModal" v-if="assignment.assignedTo.id === currentUser?.id" :aria-label="`Supprimer l'assignation ${assignment.assignedQuantity} ${groupItem.unit} pour ${groupItem.name}`"></DeleteButton>
  </div>

  <BaseModal :open="isDeleteModalOpen">
    <p>Vous etes sur le point de supprimer votre contribution :</p>
    <p>{{ groupItem.name }} : {{ assignment.assignedQuantity }} {{ groupItem.unit }}</p>
    <p>Êtes vous sûr de vouloir continuer ?</p>

    <div class="flex gap-1">
      <DeleteButton @click="deleteAssignment">Supprimer</DeleteButton>
      <CancelButton @click="toggleDeleteModal">Annuler</CancelButton>
    </div>
  </BaseModal>

</template>

<style scoped>
</style>