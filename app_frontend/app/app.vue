<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
  computed: {
    displayStore() {
      return useDisplayStore();
    }
  },
  methods: {
    updateIsDesktop() {
      this.displayStore.isDesktop = window.innerWidth > 768;
    }
  },
  mounted() {
    this.updateIsDesktop();
    window.addEventListener("resize", this.updateIsDesktop);
  },

  unmounted() {
    window.removeEventListener("resize", this.updateIsDesktop);
  }
});
</script>

<template>
  <UApp>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </UApp>
</template>