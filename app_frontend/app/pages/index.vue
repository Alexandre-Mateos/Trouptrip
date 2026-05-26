<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "index.vue"
})
</script>

<template>
<h1>Bienvenue sur Trouptrip</h1>
  <p>L'application pour organiser vos voyages en groupe.</p>
  <nav>
    <NuxtLink to="/login">Se connecter</NuxtLink>
    <NuxtLink to="/register">Créer un compte</NuxtLink>
  </nav>
</template>

<style scoped>

</style>