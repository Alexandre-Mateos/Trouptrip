<script lang="ts">
import {defineComponent} from 'vue'
import type {PropType} from 'vue'
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails"
import type {IMapGroupItem} from "~/interfaces/groupItem/i-mapGroupItem";
import type {IPersonalItem} from "~/interfaces/personalItem/I-presonalItem";
import SecondaryButton from "~/components/button/SecondaryButton.vue";
import DeleteButton from "~/components/button/DeleteButton.vue";
import EditButton from "~/components/button/EditButton.vue";
import ActionButton from "~/components/button/ActionButton.vue";
import CancelButton from "~/components/button/CancelButton.vue";

export default defineComponent({
  name: 'GroupItemTab',
  components: {CancelButton, ActionButton, EditButton, DeleteButton, SecondaryButton},
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
      isDeletePersonalItemModalOpen: false,
      isCheckListModalOpen: false,
      activeGroupItem: {} as IMapGroupItem,
      activePersonalItem: {} as IPersonalItem,
      groupItemError: '',
      personalItemError: ''
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
      this.groupItemError = '';
      try {
        await useGroupItemsStore().deleteGroupItem(this.trip, groupItem.id);

      } catch (error: any) {
        this.groupItemError = "Une erreur est survenue. Veuillez réessayer plus tard.";
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
    },
    toggleDeletePersonalItemModal(personalItem: IPersonalItem) {

      if (!this.isDeletePersonalItemModalOpen) {
        this.activePersonalItem = personalItem;
      }

      this.isDeletePersonalItemModalOpen = !this.isDeletePersonalItemModalOpen;
    },
    async deletePersonalItem(personalItem: IPersonalItem) {
      this.personalItemError = '';
      try {
        await usePersonalItemsStore().deletePersonalItem(this.trip, personalItem.id);

      } catch (error: any) {
        this.personalItemError = "Une erreur est survenue. Veuillez réessayer plus tard.";
      }

      this.toggleDeletePersonalItemModal(personalItem);
    },
    toggleChecklistModal(){
      return this.isCheckListModalOpen = !this.isCheckListModalOpen;
    }
  },
  computed: {
    currentUserId(){
      return useUserStore().user?.id;
    },
    groupItems() {
      return useGroupItemsStore().getGroupItemListByTrip(this.trip)
    },
    assignedGroupItems() {
      if (!this.currentUserId) {
        return [];
      }
      return useAssignmentsStore().getAssignedGroupItemsByUserAndTrip(this.trip.id, this.currentUserId);
    },
    personalItems() {
      return usePersonalItemsStore().getPersonalItemsByTripId(this.trip.id);
    },
    totalPersonalItemsCount(){
      return usePersonalItemsStore().getPersonalItemTotalCountByTripId(this.trip.id);
    },
    totalAssignedGroupItemsCount(){
      if(!this.currentUserId){
        return 0;
      }
      return useAssignmentsStore().getAssignedGroupItemsTotalCountByUserAndTrip(this.trip.id, this.currentUserId)
    }
  }
})
</script>

<template>

  <div class="flex flex-col gap-2">
    <Collapsible label="La liste commune">
      <section aria-label="La liste commune" class="p-2 flex flex-col gap-2">

        <div v-if="groupItemError" role="alert" class="text-red-700 bg-red-50 p-3 rounded mb-4">
          {{ groupItemError }}
        </div>
        <div v-if="personalItemError" role="alert" class="text-red-700 bg-red-50 p-3 rounded mb-4">
          {{ personalItemError }}
        </div>

        <ActionButton type="submit" @click="toggleCreateGroupItemModal" icon="fa6-solid:circle-plus">
          Ajouter un item
        </ActionButton>

        <template v-for="groupItem in groupItems" :key="groupItem.id">
          <CollapsibleCard :id="`card-body-${groupItem.id}`">
            <template #header>
              <div class="flex flex-1 justify-between items-center px-2">
                <div class="flex gap-2 items-center">

                  <Icon
                      v-if="handledQty(groupItem) === groupItem.totalQuantity"
                      name="tabler:circle-check"
                      class="text-trouptrip-success-500 text-xl fill-trouptrip-success-500"
                      aria-hidden="true"
                  ></Icon>
                  <Icon
                      v-else-if="handledQty(groupItem) > 0 && handledQty(groupItem) < groupItem.totalQuantity"
                      name="mdi-light:minus-circle"
                      class="text-trouptrip-accent-500 text-xl"
                      aria-hidden="true"
                  ></Icon>
                  <Icon
                      v-else
                      name="material-symbols:circle-outline"
                      class="text-trouptrip-error-500 text-xl"
                      aria-hidden="true"
                  ></Icon>

                  <span>
                    {{ groupItem.name }}
                  </span>
                </div>
                <div class="flex gap-1 items-center">
                  <span>
                    {{ handledQty(groupItem) }}
                  </span>
                  <span>
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
                    <EditButton
                        @click="toggleEditGroupItemModal(groupItem)"
                        class="py-2"
                        :aria-label="`Modifier ${groupItem.name}`"
                    />
                    <DeleteButton
                        @click="toggleDeleteGroupItemModal(groupItem)"
                        class="py-2"
                        :aria-label="`Supprimer ${groupItem.name}`"
                    />
                  </div>
                </div>

                <AssignmentForm :group-item="groupItem"></AssignmentForm>

                <div>
                  <p class="font-medium">Contributions :</p>
                  <template v-for="assignment in assignments(groupItem)" :key="assignment.id" class="flex flex-wrap">
                    <AssignmentLabel :assignment="assignment" :groupItem="groupItem"></AssignmentLabel>
                  </template>
                </div>
              </div>
            </template>
          </CollapsibleCard>
        </template>
      </section>
    </Collapsible>

    <Collapsible label="Mon sac à dos" default-open>
      <section aria-label="Mon sac à dos" class="p-2 flex flex-col gap-2">

        <div class="flex gap-2">
          <ActionButton type="submit" @click="toggleCreatePersonalItemModal" icon="fa6-solid:circle-plus">
            Ajouter un item
          </ActionButton>
          <SecondaryButton
              @click="toggleChecklistModal"
              icon="boxicons:check-square-filled"
          >
            Checklist
          </SecondaryButton>
        </div>

        <ChecklistProgressBar :trip-id="trip.id"></ChecklistProgressBar>

        <ItemCard
            :is-from-group-item="true"
            v-for="assignedItem in assignedGroupItems"
            :key="assignedItem.id"
            :item="assignedItem"
        ></ItemCard>

        <ItemCard v-for="personalItem in personalItems" :key="personalItem.id" :item="personalItem">
          <EditButton
              @click="toggleEditPersonalItemModal(personalItem)"
              class="py-2"
              :aria-label="`Modifier ${personalItem.name}`"
          />
          <DeleteButton
              @click="toggleDeletePersonalItemModal(personalItem)"
              class="py-2"
              :aria-label="`Supprimer ${personalItem.name}`"
          />
        </ItemCard>
      </section>
    </Collapsible>
  </div>

  <BaseModal :open="isCreateGroupItemModalOpen">
    <GroupItemForm @done="toggleCreateGroupItemModal" :trip="trip"></GroupItemForm>
  </BaseModal>

  <BaseModal :open="isEditGroupItemModalOpen">
    <GroupItemForm @done="toggleEditGroupItemModal" :trip="trip" :group-item-to-edit="activeGroupItem"></GroupItemForm>
  </BaseModal>

  <BaseModal :open="isDeleteGroupItemModalOpen">
    <div class="space-y-4">
      <p class="font-medium">Vous êtes sur le point de supprimer l'item suivant :</p>
      <p class="text-lg font-bold text-center">{{ activeGroupItem.name }}</p>
      <p class="text-sm text-gray-600">Êtes-vous sûr de vouloir continuer ?</p>
      <div class="flex flex-row justify-center gap-2">
        <DeleteButton @click="deleteGroupItem(activeGroupItem)">Supprimer</DeleteButton>
        <CancelButton @click="toggleDeleteGroupItemModal(activeGroupItem)">Annuler</CancelButton>
      </div>
    </div>
  </BaseModal>

  <BaseModal :open="isCreatePersonalItemModalOpen">
    <PersonalItemForm @done="toggleCreatePersonalItemModal" :trip="trip"></PersonalItemForm>
  </BaseModal>

  <BaseModal :open="isEditPersonalItemModalOpen">
    <PersonalItemForm @done="toggleEditPersonalItemModal" :trip="trip" :personal-item-to-edit="activePersonalItem"></PersonalItemForm>
  </BaseModal>

  <BaseModal :open="isDeletePersonalItemModalOpen">
    <div class="space-y-4">
      <p class="font-medium">Vous êtes sur le point de supprimer l'item suivant :</p>
      <p class="text-lg font-bold text-center">{{ activePersonalItem.name }}</p>
      <p class="text-sm text-gray-600">Êtes-vous sûr de vouloir continuer ?</p>
      <div class="flex flex-row justify-center gap-2">
        <DeleteButton @click="deletePersonalItem(activePersonalItem)">Supprimer</DeleteButton>
        <CancelButton @click="toggleDeletePersonalItemModal(activePersonalItem)">Annuler</CancelButton>
      </div>
    </div>
  </BaseModal>

  <BaseModal :open="isCheckListModalOpen">
    <section aria-labelledby="checklist-title" class="flex flex-col gap-2">
      <h4 id="checklist-title" class="text-lg font-bold text-center">Checklist de départ</h4>
      <ChecklistProgressBar :trip-id="trip.id"></ChecklistProgressBar>

      <div v-if="totalAssignedGroupItemsCount > 0" class="border border-solid border-trouptrip-accent-500 rounded-md p-2">
        <CheckboxItem
            v-for="assignedItem in assignedGroupItems"
            :key="assignedItem.id"
            :item="assignedItem"
            :is-from-assigned-group-item="true"
            :trip="trip"
        ></CheckboxItem>
      </div>

      <div v-if="totalPersonalItemsCount > 0" class="border border-solid border-trouptrip-accent-500 rounded-md p-2">
        <CheckboxItem
            v-for="personalItem in personalItems"
            :key="personalItem.id"
            :item="personalItem"
            :trip="trip"
        ></CheckboxItem>
      </div>

      <SecondaryButton class="m-auto" @click="toggleChecklistModal">Fermer</SecondaryButton>
    </section>
  </BaseModal>

</template>

<style scoped>
</style>