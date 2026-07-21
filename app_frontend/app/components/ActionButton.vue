<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: "ActionButton",
  props: {
    icon: {
      type: String,
      default: "tabler:circle-check"
    },
    label: {
      type: String,
    },
    type: {
      type: String as PropType<"button" | "submit" | "reset">,
      default: "button",
    },
    color: {
      type: String,
      default: "var(--color-trouptrip-success-500)"
    }
  }
})
</script>

<template>
  <BaseButton :color="color" :type="type">
    <Icon :name="icon" />
    <slot>{{label}}</slot>
  </BaseButton>
</template>