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
  >
    <slot></slot>
  </button>
</template>

<style scoped>

</style>