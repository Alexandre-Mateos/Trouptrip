<script lang="ts">
import { defineComponent } from 'vue'
import type { PropType } from 'vue'

import type { IMapTripDetails } from '~/interfaces/trip/store/i-mapTripDetails'
import type { IPersonalItem } from '~/interfaces/personalItem/I-presonalItem'

import SecondaryButton from '~/components/button/SecondaryButton.vue'
import DeleteButton from '~/components/button/DeleteButton.vue'
import EditButton from '~/components/button/EditButton.vue'
import ActionButton from '~/components/button/ActionButton.vue'
import CancelButton from '~/components/button/CancelButton.vue'
import PersonalItemForm from "~/components/form/PersonalItemForm.vue";

export default defineComponent({
  name: 'BackpackSection',

  components: {
    PersonalItemForm,
    SecondaryButton,
    DeleteButton,
    EditButton,
    ActionButton,
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
      isCreatePersonalItemModalOpen: false,
      isEditPersonalItemModalOpen: false,
      isDeletePersonalItemModalOpen: false,
      isChecklistModalOpen: false,

      activePersonalItem: {} as IPersonalItem,

      personalItemError: ''
    }
  },

  computed: {
    currentUserId() {
      return useUserStore().user?.id
    },

    assignedGroupItems() {
      if (!this.currentUserId) {
        return []
      }

      return useAssignmentsStore()
          .getAssignedGroupItemsByUserAndTrip(
              this.trip.id,
              this.currentUserId
          )
    },

    personalItems() {
      return usePersonalItemsStore()
          .getPersonalItemsByTripId(this.trip.id)
    }
  },

  methods: {
    toggleCreatePersonalItemModal() {
      this.isCreatePersonalItemModalOpen =
          !this.isCreatePersonalItemModalOpen
    },

    toggleEditPersonalItemModal(
        personalItem: IPersonalItem
    ) {
      if (!this.isEditPersonalItemModalOpen) {
        this.activePersonalItem = personalItem
      }

      this.isEditPersonalItemModalOpen =
          !this.isEditPersonalItemModalOpen
    },

    toggleDeletePersonalItemModal(
        personalItem: IPersonalItem
    ) {
      if (!this.isDeletePersonalItemModalOpen) {
        this.activePersonalItem = personalItem
      }

      this.isDeletePersonalItemModalOpen =
          !this.isDeletePersonalItemModalOpen
    },

    async deletePersonalItem(
        personalItem: IPersonalItem
    ) {
      this.personalItemError = ''

      try {
        await usePersonalItemsStore()
            .deletePersonalItem(
                this.trip,
                personalItem.id
            )
      } catch {
        this.personalItemError =
            'Une erreur est survenue. Veuillez réessayer plus tard.'
      }

      this.toggleDeletePersonalItemModal(personalItem)
    },

    toggleChecklistModal() {
      this.isChecklistModalOpen =
          !this.isChecklistModalOpen
    }
  }
})
</script>

<template>
  <Collapsible
      label="Mon sac à dos"
      default-open
  >
    <section
        aria-label="Mon sac à dos"
        class="p-2 flex flex-col gap-2"
    >
      <div
          v-if="personalItemError"
          role="alert"
          class="text-red-700 bg-red-50 p-3 rounded mb-4"
      >
        {{ personalItemError }}
      </div>

      <div class="flex gap-2">
        <ActionButton
            type="submit"
            icon="fa6-solid:circle-plus"
            @click="toggleCreatePersonalItemModal"
        >
          Ajouter un item
        </ActionButton>

        <SecondaryButton
            icon="boxicons:check-square-filled"
            @click="toggleChecklistModal"
        >
          Checklist
        </SecondaryButton>
      </div>

      <ChecklistProgressBar :trip-id="trip.id" />

      <ItemCard
          v-for="assignedItem in assignedGroupItems"
          :key="assignedItem.id"
          :item="assignedItem"
          :is-from-group-item="true"
      />

      <ItemCard
          v-for="personalItem in personalItems"
          :key="personalItem.id"
          :item="personalItem"
      >
        <EditButton
            class="py-2"
            :aria-label="`Modifier ${personalItem.name}`"
            @click="
            toggleEditPersonalItemModal(personalItem)
          "
        />

        <DeleteButton
            class="py-2"
            :aria-label="`Supprimer ${personalItem.name}`"
            @click="
            toggleDeletePersonalItemModal(personalItem)
          "
        />
      </ItemCard>
    </section>
  </Collapsible>

  <BaseModal :open="isCreatePersonalItemModalOpen">
    <PersonalItemForm
        :trip="trip"
        @done="toggleCreatePersonalItemModal"
    />
  </BaseModal>

  <BaseModal :open="isEditPersonalItemModalOpen">
    <PersonalItemForm
        :trip="trip"
        :personal-item-to-edit="activePersonalItem"
        @done="
        toggleEditPersonalItemModal(activePersonalItem)
      "
    />
  </BaseModal>

  <BaseModal :open="isDeletePersonalItemModalOpen">
    <div class="space-y-4">
      <p class="font-medium">
        Vous êtes sur le point de supprimer l'item suivant :
      </p>

      <p class="text-lg font-bold text-center">
        {{ activePersonalItem.name }}
      </p>

      <p class="text-sm text-gray-600">
        Êtes-vous sûr de vouloir continuer ?
      </p>

      <div class="flex flex-row justify-center gap-2">
        <DeleteButton
            @click="deletePersonalItem(activePersonalItem)"
        >
          Supprimer
        </DeleteButton>

        <CancelButton
            @click="
            toggleDeletePersonalItemModal(
              activePersonalItem
            )
          "
        >
          Annuler
        </CancelButton>
      </div>
    </div>
  </BaseModal>

  <ChecklistModal
      :open="isChecklistModalOpen"
      :trip="trip"
      @close="toggleChecklistModal"
  />
</template>