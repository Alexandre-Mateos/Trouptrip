<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "Footer",
  computed: {
    footerNav() {
      return useDisplayStore().footerNav
    }
  }
})
</script>

<template>
  <UFooter
      :ui="{
        root: 'bg-surface-primary-trouptrip border-none mt-4',
        container: 'bg-surface-primary-trouptrip text-trouptrip-title w-72 max-w-xs',
      }"
  >
    <template #left>
      <p class="text-muted text-sm">Copyright © 2026</p>
    </template>
    <UNavigationMenu :items="footerNav" variant="link" />
  </UFooter>
</template>

<style scoped>

</style>