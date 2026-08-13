<script lang="ts">
import { defineComponent, type PropType } from 'vue'
import BaseButton from "~/components/button/BaseButton.vue";

export default defineComponent({
  name: "SecondaryButton",
  components: {BaseButton},
  props: {
    type: {
      type: String as PropType<'button' | 'submit' | 'reset'>,
      default: 'button'
    }
  }
})
</script>

<template>
  <BaseButton
      :type="type"
      class="bg-white text-trouptrip-accent-700 border border-trouptrip-accent-600 hover:bg-trouptrip-accent-50"
  >
    <slot />
  </BaseButton>
</template>