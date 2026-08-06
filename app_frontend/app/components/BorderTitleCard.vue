<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "BorderTitleCard",
  props:{
    title:{
      type: String,
      required: true
    }
  }
})
</script>

<template>
  <div>
  <div class="text-center">
    <h3 class="inline-block bg-white px-3 translate-y-3">
      {{title}}
    </h3>
  </div>
  <div class="rounded-md p-4 inset-shadow-sm border border-trouptrip-accent-200">
    <slot></slot>
  </div>
  </div>
</template>

<style scoped>

</style>