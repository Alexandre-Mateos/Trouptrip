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
  }
})
</script>

<template>
  <button
      v-bind="$attrs"
      :type="type"
      class="inline-flex items-center justify-center gap-1 px-3 py-1 rounded-md w-max cursor-pointer transition-colors"
  >
    <slot />
  </button>
</template>