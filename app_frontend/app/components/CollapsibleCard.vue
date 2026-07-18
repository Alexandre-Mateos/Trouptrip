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
  <UCard>
    <div class="flex items-center justify-between cursor-pointer select-none" @click="toggleCard">
      <slot name="header"></slot>

      <Icon name="i-lucide-chevron-down"
            :class="[
              'transition-transform duration-200',
              isExpanded ? 'rotate-180' : ''
            ]"
      />
    </div>

    <div
        :class="[
        'grid transition-[grid-template-rows] duration-300 ease-in-out',
        isExpanded ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'
      ]"
    >
      <div class="overflow-hidden">
        <div class="pt-4">
          <slot name="body"></slot>
        </div>
      </div>
    </div>
  </UCard>
</template>