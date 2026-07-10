<script lang="ts">
import { defineComponent, type PropType } from 'vue'

export default defineComponent({
  name: "BaseButton",
  inheritAttrs: false,
  props: {
    type: {
      type: String as PropType<'button' | 'submit' | 'reset'>,
      default: 'button'
    },
    color: {
      type: String,
      default: 'var(--color-trouptrip-accent-500)'
    }
  },
})
</script>

<template>
  <button
      :type="type"
      v-bind="$attrs"
      class="inline-flex items-center justify-center gap-1 px-3 py-1 rounded-md w-max cursor-pointer"
  >
    <slot></slot>
  </button>
</template>

<style scoped>
button {
  background-color: v-bind(color);
  color: white;
}
</style>