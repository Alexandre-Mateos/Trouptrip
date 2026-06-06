<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "BaseForm",
  emits: ['submit']
})
</script>

<template>
  <form @submit.prevent="$emit('submit')"
        class="p-6 max-w-xs md:max-w-md flex flex-col gap-4 rounded-md mx-auto"
  >
    <slot></slot>
  </form>
</template>

<style scoped>
form{
  background-color: var(--color-surface);
}

</style>