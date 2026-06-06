<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "Button",
  props: {
    type: {
      type: String as PropType<'submit'>,
      default: 'submit'
    }
  },
})
</script>

<template>
  <button
      :type="type"
      class="px-3 py-1 rounded-md"
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