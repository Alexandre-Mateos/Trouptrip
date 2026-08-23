<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: "BorderTitleCard",
  props: {
    title: {
      type: String,
      required: true
    }
  }
})
</script>

<template>
  <section>
    <div class="text-center">
      <h4 class="inline-block bg-white px-3 translate-y-3 font-semibold">
        {{ title }}
      </h4>
    </div>
    <div class="rounded-md p-4 inset-shadow-sm border border-trouptrip-accent-200">
      <slot></slot>
    </div>
  </section>
</template>