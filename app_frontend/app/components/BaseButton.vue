<script lang="ts">
import { defineComponent, type PropType } from 'vue'

export default defineComponent({
  name: "BaseButton",
  inheritAttrs: false,
  props: {
    type: {
      type: String as PropType<'button' | 'submit' | 'reset'>,
      default: 'button'
    }
  },
})
</script>

<template>
  <button
      :type="type"
      v-bind="$attrs"
      class="px-3 py-1 rounded-md w-max"
  >
    <slot></slot>
  </button>
</template>

<style scoped>
button {
  background-color: var(--color-accent);
  color: white;
}
</style>