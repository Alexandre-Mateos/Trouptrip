<script lang="ts">
import {defineComponent} from 'vue'
import LogoutButton from "~/components/LogoutButton.vue";

export default defineComponent({
  name: "Header",
  components: {LogoutButton},
  data() {
    return {
      unLoggedUserLinks: [
        {to: '/', label: 'Accueil'},
        {to: '/login', label: 'Se connecter'},
        {to: '/register', label: 'Créer un compte'}
      ],
      loggedUserLinks: [
        {to: '/home', label: 'Mon espace'}
      ]
    }
  },
  computed: {
    // On mappe le store en tant que propriété calculée
    userStore() {
      return useUserStore()
    },
  },
})
</script>

<template>
  <header class="collapse shadow-sm w-full">
    <input id="navbar-1-toggle" class="peer hidden" type="checkbox"/>
    <div class="collapse-title cursor-default navbar p-0 container mx-auto px-4 md:px-6 lg:px-8">
      <div class="navbar-start">
        <NuxtLink to="/"><img src="/trouptrip_logo_no_background.png" alt="logo de Trouptrip" title="retour à l'accueil"
                              class="h-10 w-auto"></NuxtLink>
      </div>

      <div class="navbar-center">
        <p>Trouptrip</p>
      </div>

      <div class="navbar-end">
        <div class="hidden lg:flex">

          <div v-if="userStore.user">
            <BaseNav :links="loggedUserLinks" class="menu menu-horizontal px-1 gap-2">
              <LogoutButton></LogoutButton>
            </BaseNav>
          </div>
          <div v-else>
            <BaseNav :links="unLoggedUserLinks" class="menu menu-horizontal px-1 gap-2"></BaseNav>
          </div>
        </div>

        <label for="navbar-1-toggle" class="lg:hidden cursor-pointer toggle-btn p-2 rounded-md">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
          </svg>
        </label>
      </div>
    </div>

    <div class="collapse-content lg:hidden z-1 container mx-auto px-4 md:px-6 lg:px-8">
      <div v-if="userStore.user">
        <BaseNav :links="loggedUserLinks" class="menu flex flex-column gap-2">
          <LogoutButton></LogoutButton>
        </BaseNav>
      </div>
      <div v-else>
        <BaseNav :links="unLoggedUserLinks" class="menu flex flex-column gap-2"></BaseNav>
      </div>
    </div>

  </header>

</template>

<style scoped>
header {
  background-color: var(--color-surface);
}

.toggle-btn:hover {
  background-color: var(--color-accent);
  color: #ffffff;
}
</style>