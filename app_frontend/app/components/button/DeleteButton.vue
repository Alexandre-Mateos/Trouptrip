<script lang="ts">
import { defineComponent, type PropType } from 'vue'
import BaseButton from "~/components/button/BaseButton.vue";

export default defineComponent({
  name: "DeleteButton",
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
      class="bg-trouptrip-error-700 text-white hover:bg-trouptrip-error-800"
  >
    <Icon name="lucide:trash-2" />
    <slot></slot>
  </BaseButton>
</template>