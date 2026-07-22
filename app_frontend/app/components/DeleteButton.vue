<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: "DeleteButton",
  props: {
    label: {
      type: String
    }
  }
})
</script>

<template>
  <BaseButton color="var(--color-trouptrip-error-600)">
    <Icon name="material-symbols:delete-outline" />
    <slot>{{ label }}</slot>
  </BaseButton>
</template>

<style scoped>

</style>