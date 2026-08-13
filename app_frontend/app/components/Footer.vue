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
      root: 'bg-white mt-4 border-t border-trouptrip-neutral-200',
      container: 'flex justify-center items-center gap-6'
    }"
  >
    <p class="text-trouptrip-neutral-600 text-sm">
      Copyright © 2026
    </p>

    <UNavigationMenu
        :items="footerNav"
        variant="link"
        :ui="{
        link: 'text-trouptrip-neutral-600 hover:text-trouptrip-neutral-800'
      }"
    />
  </UFooter>
</template>

<style scoped>

</style>