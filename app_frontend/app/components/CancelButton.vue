<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "CancelButton",
  props:{
    type: {
      type: String as PropType<"button" | "submit" | "reset">,
      default: "button",
    },
  }
})
</script>

<template>
  <BaseButton :type="type">
    <Icon name="tabler:arrow-back"/>
    <slot>Annuler</slot>
  </BaseButton>
</template>

<style scoped>

</style>