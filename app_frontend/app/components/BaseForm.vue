<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "BaseForm",
  emits: ['submit']
})
</script>

<template>
  <form @submit.prevent="$emit('submit')">
    <slot></slot>
  </form>
</template>

<style scoped>

</style>