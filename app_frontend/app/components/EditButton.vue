<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "EditButton",
  props: {
    label: {
      type: String,
    }
  }
})
</script>

<template>
  <BaseButton color="var(--color-trouptrip-secondary-600)">
    <Icon name="lucide:edit"></Icon>
    <slot>{{ label }}</slot>
  </BaseButton>
</template>

<style scoped>

</style>