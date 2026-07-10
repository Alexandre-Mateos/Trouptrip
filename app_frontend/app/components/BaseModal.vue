<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: "BaseModal",
  inheritAttrs: false,
  props: {
    open: {
      type: Boolean,
      default: false
    }
  }
})
</script>

<template>

  <UModal v-model:open="open"
          :close="false"
          :ui="{
            body: 'bg-white border-none ring-0'
          }"
  >
    <template #body>
      <slot></slot>
    </template>
  </UModal>

</template>

<style scoped>

</style>