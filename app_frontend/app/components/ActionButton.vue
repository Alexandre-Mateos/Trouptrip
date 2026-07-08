<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: "ActionButton",
  props: {
    icon: {
      type: String,
      default: "qlementine-icons:success-16"
    },
    label: {
      type: String,
      default: 'Valider'
    },
    type: {
      type: String as PropType<"button" | "submit" | "reset">,
      default: "button",
    },
  }
})
</script>

<template>
  <BaseButton color="var(--color-trouptrip-success-500)" :type="type">
    <Icon :name="icon" />
    <slot>{{label}}</slot>
  </BaseButton>
</template>