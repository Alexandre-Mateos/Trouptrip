<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: "GroupItemCard",
  data() {
    return {
      isExpanded: false
    }
  },
  methods: {
    toggleCard() {
      this.isExpanded = !this.isExpanded
    }
  }
})
</script>

<template>
  <UCard
      :ui="{
    root: 'bg-white shadow-md transform border border-solid border-trouptrip-accent-500 ring-0'
  }"
  >
    <button
        class="flex w-full items-center justify-between text-left cursor-pointer"
         type="button"
         :aria-controls="$attrs.id as string"
         :aria-expanded="isExpanded"
         @click="toggleCard"
    >
      <slot name="header"></slot>

      <Icon name="i-lucide-chevron-down"
            :class="[
              'transition-transform duration-200',
              isExpanded ? 'rotate-180' : ''
            ]"
      />
    </button>

    <div
        :class="[
        'grid transition-[grid-template-rows] duration-300 ease-in-out',
        isExpanded ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'
      ]"
        v-bind="$attrs"
        :inert="!isExpanded"
        :aria-hidden="!isExpanded"
    >
      <div class="overflow-hidden">
        <div class="pt-4">
          <slot name="body"></slot>
        </div>
      </div>
    </div>
  </UCard>
</template>