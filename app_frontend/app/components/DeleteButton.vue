<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: "DeleteButton",
  props: {
    label: {
      type: String
    },
    icon: {
      type: String,
      default: "material-symbols:delete-outline"
    },
  }
})
</script>

<template>
  <BaseButton color="var(--color-trouptrip-error-600)">
    <Icon :name="icon" />
    <slot>{{ label }}</slot>
  </BaseButton>
</template>

<style scoped>

</style>