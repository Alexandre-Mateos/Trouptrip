<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "GroupItemCard",
  data() {
    return {
      isExpanded: false
    }
  },
  methods: {
    toggleCard(){
      this.isExpanded = !this.isExpanded
    }
  }
})
</script>

<template>
  <UCard
  >
    <div class="flex items-center justify-between" @click="toggleCard">
      <slot name="header">
      </slot>

      <Icon name="i-lucide-chevron-down"
            :class="[
              'transition-transform duration-200',
              isExpanded ? 'rotate-180' : ''
            ]"
      />
    </div>

    <div
        v-if="isExpanded"
    >
      <slot name="body">

      </slot>
    </div>
  </UCard>
</template>

<style scoped>

</style>