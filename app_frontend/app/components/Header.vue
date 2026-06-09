<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "Header",
  computed: {
    // On mappe le store en tant que propriété calculée
    userStore() {
      return useUserStore()
    },
  },
})
</script>

<template>

  <!-- Header si l'utilisateur est connecté -->
  <div v-if="userStore.user">
    <nav class="flex justify-between px-5 md:px-10 py-5">
      <NuxtLink to="/"><img src="/trouptrip_logo_no_background.png" alt="logo de Trouptrip" title="retour à l'accueil"
                            class="h-10 w-auto"></NuxtLink>
      <ul class="flex gap-3">

      </ul>
    </nav>
  </div>

  <!--  header si l'utilisateur est déconnecté-->
  <div v-else>
    <nav class="flex justify-between px-5 md:px-10 py-5">
      <NuxtLink to="/"><img src="/trouptrip_logo_no_background.png" alt="logo de Trouptrip" title="retour à l'accueil"
                            class="h-10 w-auto"></NuxtLink>
      <ul class="flex gap-3">
        <li>
          <BaseButton>
            <NuxtLink to="/login">Se connecter</NuxtLink>
          </BaseButton>
        </li>
        <li>
          <BaseButton>
            <NuxtLink to="/register">Créer un compte</NuxtLink>
          </BaseButton>
        </li>
      </ul>
    </nav>
  </div>
</template>

<style scoped>
nav {
  background-color: var(--color-surface);
}
</style>