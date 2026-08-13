<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "Collapsible",
  props: {
    label: {
      type: String
    }
  }
})
</script>

<template>
  <UCollapsible class="flex flex-col"
  >
    <UButton
        class="group bg-surface-primary-trouptrip hover:bg-trouptrip-accent-600 hover:text-white cursor-pointer"
        :label="label"
        trailing-icon="i-lucide-chevron-down"
        :ui="{
        trailingIcon: 'group-data-[state=open]:rotate-180 transition-transform duration-200'
      }"
        block
    />

    <template #content>
      <slot>

      </slot>
    </template>
  </UCollapsible>
</template>

<style scoped>

</style>