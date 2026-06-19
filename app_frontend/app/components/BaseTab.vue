<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "BaseTab"
})
</script>

<template>
  <div class="py-1 px-2 bg-red cursor-pointer rounded-md basic-tab">
    <slot></slot>
  </div>
</template>

<style scoped>
.basic-tab{
  background-color: var(--color-surface);
}

.basic-tab:hover{
  background-color: var(--color-accent);
  color: #ffffff;
}
</style>