<template>
  <div class="min-h-screen main-theme">
    <Header></Header>
    <main>
      <slot />
    </main>
  </div>
</template>

<style scoped>
.main-theme{
  background-color: var(--color-bg);
  color: var(--color-text);
}
</style>