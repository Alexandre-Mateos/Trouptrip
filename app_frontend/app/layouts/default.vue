<template>
  <div class="min-h-screen main-theme">
    <Header class="mb-4"></Header>
    <main class="container mx-auto px-4 md:px-6 lg:px-8">
      <slot />
    </main>
  </div>
</template>

<style scoped>
.main-theme{
  background-color: var(--color-surface-secondary-trouptrip);
  color: var(--color-trouptrip-title);
}
</style>