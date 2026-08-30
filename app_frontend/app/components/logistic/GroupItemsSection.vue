<script lang="ts">
import { defineComponent } from 'vue'
import type { PropType } from 'vue'

import type { IMapTripDetails } from '~/interfaces/trip/store/i-mapTripDetails'
import type { IMapGroupItem } from '~/interfaces/groupItem/i-mapGroupItem'

import DeleteButton from '~/components/button/DeleteButton.vue'
import EditButton from '~/components/button/EditButton.vue'
import ActionButton from '~/components/button/ActionButton.vue'
import CancelButton from '~/components/button/CancelButton.vue'
import GroupItemForm from "~/components/form/GroupItemForm.vue";
import AssignmentForm from "~/components/form/AssignmentForm.vue";

export default defineComponent({
  name: 'GroupItemsSection',

  components: {
    AssignmentForm,
    GroupItemForm,
    ActionButton,
    EditButton,
    DeleteButton,
    CancelButton
  },

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

      activeGroupItem: {} as IMapGroupItem,

      groupItemError: ''
    }
  },

  computed: {
    groupItems() {
      return useGroupItemsStore()
          .getGroupItemListByTrip(this.trip)
    }
  },

  methods: {
    handledQty(groupItem: IMapGroupItem) {
      return useAssignmentsStore()
          .getHandledQtyByGroupItem(groupItem)
    },

    assignments(groupItem: IMapGroupItem) {
      return useAssignmentsStore()
          .getAssignmentsByGroupItem(groupItem)
    },

    toggleCreateGroupItemModal() {
      this.isCreateGroupItemModalOpen =
          !this.isCreateGroupItemModalOpen
    },

    toggleEditGroupItemModal(groupItem: IMapGroupItem) {
      if (!this.isEditGroupItemModalOpen) {
        this.activeGroupItem = groupItem
      }

      this.isEditGroupItemModalOpen =
          !this.isEditGroupItemModalOpen
    },

    toggleDeleteGroupItemModal(groupItem: IMapGroupItem) {
      if (!this.isDeleteGroupItemModalOpen) {
        this.activeGroupItem = groupItem
      }

      this.isDeleteGroupItemModalOpen =
          !this.isDeleteGroupItemModalOpen
    },

    async deleteGroupItem(groupItem: IMapGroupItem) {
      this.groupItemError = ''

      try {
        await useGroupItemsStore()
            .deleteGroupItem(this.trip, groupItem.id)
      } catch {
        this.groupItemError =
            'Une erreur est survenue. Veuillez réessayer plus tard.'
      }

      this.toggleDeleteGroupItemModal(groupItem)
    }
  }
})
</script>

<template>
  <Collapsible label="La liste commune">
    <section
        aria-label="La liste commune"
        class="p-2 flex flex-col gap-2"
    >
      <div
          v-if="groupItemError"
          role="alert"
          class="text-red-700 bg-red-50 p-3 rounded mb-4"
      >
        {{ groupItemError }}
      </div>

      <ActionButton
          type="submit"
          icon="fa6-solid:circle-plus"
          @click="toggleCreateGroupItemModal"
      >
        Ajouter un item
      </ActionButton>

      <template
          v-for="groupItem in groupItems"
          :key="groupItem.id"
      >
        <CollapsibleCard :id="`card-body-${groupItem.id}`">
          <template #header>
            <div
                class="flex flex-1 justify-between items-center px-2"
            >
              <div class="flex gap-2 items-center">
                <Icon
                    v-if="
                    handledQty(groupItem) ===
                    groupItem.totalQuantity
                  "
                    name="tabler:circle-check"
                    class="
                    text-trouptrip-success-500
                    text-xl
                    fill-trouptrip-success-500
                  "
                    aria-hidden="true"
                />

                <Icon
                    v-else-if="
                    handledQty(groupItem) > 0 &&
                    handledQty(groupItem) <
                      groupItem.totalQuantity
                  "
                    name="mdi-light:minus-circle"
                    class="text-trouptrip-accent-500 text-xl"
                    aria-hidden="true"
                />

                <Icon
                    v-else
                    name="material-symbols:circle-outline"
                    class="text-trouptrip-error-500 text-xl"
                    aria-hidden="true"
                />

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
                      class="py-2"
                      :aria-label="`Modifier ${groupItem.name}`"
                      @click="
                      toggleEditGroupItemModal(groupItem)
                    "
                  />

                  <DeleteButton
                      class="py-2"
                      :aria-label="`Supprimer ${groupItem.name}`"
                      @click="
                      toggleDeleteGroupItemModal(groupItem)
                    "
                  />
                </div>
              </div>

              <AssignmentForm
                  :group-item="groupItem"
              />

              <div>
                <p class="font-medium">
                  Contributions :
                </p>

                <template
                    v-for="assignment in assignments(groupItem)"
                    :key="assignment.id"
                >
                  <AssignmentLabel
                      :assignment="assignment"
                      :group-item="groupItem"
                  />
                </template>
              </div>
            </div>
          </template>
        </CollapsibleCard>
      </template>
    </section>
  </Collapsible>

  <BaseModal :open="isCreateGroupItemModalOpen">
    <GroupItemForm
        :trip="trip"
        @done="toggleCreateGroupItemModal"
    />
  </BaseModal>

  <BaseModal :open="isEditGroupItemModalOpen">
    <GroupItemForm
        :trip="trip"
        :group-item-to-edit="activeGroupItem"
        @done="toggleEditGroupItemModal(activeGroupItem)"
    />
  </BaseModal>

  <BaseModal :open="isDeleteGroupItemModalOpen">
    <div class="space-y-4">
      <p class="font-medium">
        Vous êtes sur le point de supprimer l'item suivant :
      </p>

      <p class="text-lg font-bold text-center">
        {{ activeGroupItem.name }}
      </p>

      <p class="text-sm text-gray-600">
        Êtes-vous sûr de vouloir continuer ?
      </p>

      <div class="flex flex-row justify-center gap-2">
        <DeleteButton
            @click="deleteGroupItem(activeGroupItem)"
        >
          Supprimer
        </DeleteButton>

        <CancelButton
            @click="
            toggleDeleteGroupItemModal(activeGroupItem)
          "
        >
          Annuler
        </CancelButton>
      </div>
    </div>
  </BaseModal>
</template>