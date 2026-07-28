<script lang="ts">
import {defineComponent} from 'vue'
import type {PropType} from 'vue'
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails"
import type {IMapGroupItem} from "~/interfaces/groupItem/i-mapGroupItem";
import type {IPersonalItem} from "~/interfaces/personalItem/I-presonalItem";

export default defineComponent({
  name: 'GroupItemTab',
  props: {
    trip: {
      type: Object as PropType<IMapTripDetails>,
      required: true
    }
  },
  data() {
    return {
      isCreateGroupItemModalOpen: false,
      isEditGroupItemModalOpen: false,
      isDeleteGroupItemModalOpen: false,
      isCreatePersonalItemModalOpen: false,
      isEditPersonalItemModalOpen: false,
      activeGroupItem: {} as IMapGroupItem,
      activePersonalItem: {} as IPersonalItem,
      error: ''
    }
  },
  methods: {
    handledQty(groupItem: IMapGroupItem) {
      return useAssignmentsStore().getHandledQtyByGroupItem(groupItem)
    },
    assignments(groupItem: IMapGroupItem) {
      return useAssignmentsStore().getAssignmentsByGroupItem(groupItem)
    },
    toggleCreateGroupItemModal() {
      this.isCreateGroupItemModalOpen = !this.isCreateGroupItemModalOpen;
    },
    toggleEditGroupItemModal(groupItem: IMapGroupItem) {

      if (!this.isEditGroupItemModalOpen) {
        this.activeGroupItem = groupItem;
      }

      this.isEditGroupItemModalOpen = !this.isEditGroupItemModalOpen;
    },
    toggleDeleteGroupItemModal(groupItem: IMapGroupItem) {

      if (!this.isDeleteGroupItemModalOpen) {
        this.activeGroupItem = groupItem;
      }

      this.isDeleteGroupItemModalOpen = !this.isDeleteGroupItemModalOpen;
    },
    async deleteGroupItem(groupItem: IMapGroupItem) {
      this.error = '';
      try {
        await useGroupItemsStore().deleteGroupItem(this.trip, groupItem.id);

      } catch (error: any) {
        this.error = "Une erreur est survenue. Veuillez réessayer plus tard.";
      }

      this.toggleDeleteGroupItemModal(groupItem);
    },
    toggleCreatePersonalItemModal(){
      this.isCreatePersonalItemModalOpen = !this.isCreatePersonalItemModalOpen;
    },
    toggleEditPersonalItemModal(personalItem: IPersonalItem){
      if (!this.isEditPersonalItemModalOpen) {
        this.activePersonalItem = personalItem;
      }

      this.isEditPersonalItemModalOpen = !this.isEditPersonalItemModalOpen;
    }
  },
  computed: {
    groupItems() {
      return useGroupItemsStore().getGroupItemListByTrip(this.trip)
    },
    assignedGroupItems() {
      const currentUserId = useUserStore().user?.id;
      if (!currentUserId) {
        return [];
      }
      return useAssignmentsStore().getAssignedGroupItemsByUserAndTrip(this.trip.id, currentUserId);
    },
    personalItems() {
      return usePersonalItemsStore().getPersonalItemsByTripId(this.trip.id);
    }
  }
})
</script>

<template>

  <div class="flex flex-col gap-2">
    <Collapsible label="La valise">
      <div class="p-2 flex flex-col gap-2">

        <div v-if="error" class="text-red-700 p-3 rounded mb-4">
          {{ error }}
        </div>

        <ActionButton type="submit" @click="toggleCreateGroupItemModal" icon="fa6-solid:circle-plus">Ajouter un item</ActionButton>

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
                      name="material-symbols:circle-outline"
                      class="text-trouptrip-error-500 text-xl"
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

              <div class="flex flex-col gap-1">
                <div class="flex justify-end">
                  <div class="flex flex-row gap-2">
                    <EditButton @click="toggleEditGroupItemModal(groupItem)" class="py-2"></EditButton>
                    <DeleteButton @click="toggleDeleteGroupItemModal(groupItem)" class="py-2"></DeleteButton>
                  </div>
                </div>

                <AssignmentForm :group-item="groupItem"></AssignmentForm>

                <div>
                  <p>Contributions :</p>
                  <template v-for="assignment in assignments(groupItem)" :key="assignment.id" class="flex flex-wrap">
                    <AssignmentLabel :assignment="assignment" :groupItem="groupItem"></AssignmentLabel>
                  </template>
                </div>
              </div>

            </template>
          </CollapsibleCard>
        </template>
      </div>
    </Collapsible>

    <Collapsible label="Mon sac à dos">
      <div class="p-2 flex flex-col gap-2">
        <ActionButton type="submit" @click="toggleCreatePersonalItemModal" icon="fa6-solid:circle-plus">Ajouter un item</ActionButton>

        <ItemCard :is-from-group-item="true" v-for="assignedItem in assignedGroupItems"
                  :key="assignedItem.id" :item="assignedItem" ></ItemCard>

        <ItemCard v-for="personalItem in personalItems" :key="personalItem.id" :item="personalItem">
          <EditButton @click="toggleEditPersonalItemModal(personalItem)" class="py-2"></EditButton>
        </ItemCard>
      </div>
    </Collapsible>
  </div>

  <BaseModal :open="isCreateGroupItemModalOpen">
    <GroupItemForm @done="toggleCreateGroupItemModal" :trip="trip"></GroupItemForm>
  </BaseModal>

  <BaseModal :open="isEditGroupItemModalOpen">
    <GroupItemForm @done="toggleEditGroupItemModal" :trip="trip" :group-item-to-edit="activeGroupItem"></GroupItemForm>
  </BaseModal>

  <BaseModal :open="isDeleteGroupItemModalOpen">
    <div>
      <p>Vous Etes sur le point de supprimer l'item suivant :</p>
      <p>{{ activeGroupItem.name }}</p>
      <p>Etes vous sûr de vouloir continuer ?</p>
      <div class="flex flex-row justify-center gap-2">
        <DeleteButton @click="deleteGroupItem(activeGroupItem)" label="Supprimer"></DeleteButton>
        <CancelButton @click="toggleDeleteGroupItemModal"></CancelButton>
      </div>
    </div>
  </BaseModal>

  <BaseModal :open="isCreatePersonalItemModalOpen">
      <PersonalItemForm @done="toggleCreatePersonalItemModal" :trip="trip" ></PersonalItemForm>
  </BaseModal>

  <BaseModal :open="isEditPersonalItemModalOpen">
    <PersonalItemForm @done="toggleEditPersonalItemModal" :trip="trip" :personal-item-to-edit="activePersonalItem" ></PersonalItemForm>
  </BaseModal>

</template>

<style scoped>

</style>