<template>
  <div class="main-theme flex flex-col min-h-screen">
    <Header class="mb-4" id="main-header" tabindex="-1"></Header>
    <main class="container mx-auto px-4 md:px-6 lg:px-8 flex-1">
      <slot/>
    </main>
    <Footer></Footer>
  </div>
</template>

<style scoped>
.main-theme{
  background-color: var(--color-trouptrip-neutral-100);
  color: var(--color-trouptrip-title);
}
</style>
<script setup lang="ts">
</script>