<script lang="ts">
import {defineComponent} from "vue";

export default defineComponent({
  data() {
    return {
      windowWidth: typeof window !== 'undefined' ? window.innerWidth : 1024,
    }
  },
  computed: {
    displayStore() {
      return useDisplayStore();
    }
  },
  methods: {
    handleResize() {
      this.windowWidth = window.innerWidth;
      this.displayStore.isDesktop = this.windowWidth > 768;
    },
  },
  mounted() {
    this.handleResize();

    window.addEventListener('resize', this.handleResize);
  },
  unmounted() {
    window.removeEventListener('resize', this.handleResize);
  }
});
</script>

<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
</template>