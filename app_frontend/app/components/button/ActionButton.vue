<script lang="ts">
import { defineComponent, type PropType } from 'vue'
import BaseButton from "~/components/button/BaseButton.vue";

export default defineComponent({
  name: "ActionButton",
  components: {BaseButton},
  props: {
    icon: {
      type: String
    },
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
      class="bg-trouptrip-accent-600 text-white hover:bg-trouptrip-accent-700"
  >
    <Icon
        v-if="icon"
        :name="icon"
    />

    <slot></slot>
  </BaseButton>
</template>