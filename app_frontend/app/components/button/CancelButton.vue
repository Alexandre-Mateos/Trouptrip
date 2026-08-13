<script lang="ts">
import { defineComponent, type PropType } from 'vue'
import SecondaryButton from "~/components/button/SecondaryButton.vue";

export default defineComponent({
  name: "CancelButton",
  components: {SecondaryButton},
  props: {
    type: {
      type: String as PropType<'button' | 'submit' | 'reset'>,
      default: 'button'
    }
  }
})
</script>

<template>
  <SecondaryButton :type="type">
    <Icon name="tabler:arrow-back" />
    <slot>Annuler</slot>
  </SecondaryButton>
</template>