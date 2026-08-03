<script setup lang="ts">
defineOptions({
  inheritAttrs: false
})

const open = defineModel<boolean>('open', { default: false })
</script>

<template>
  <UModal
      v-model:open="open"
      :close="false"
      :ui="{
        body: 'bg-white border-none ring-0'
      }"
  >
    <template #body>
      <slot />
    </template>
  </UModal>
</template>