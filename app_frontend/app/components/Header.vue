<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "Header"
})
</script>

<template>
  <nav>
    <NuxtLink to="/">Accueil</NuxtLink>
    <NuxtLink to="/login">Se connecter</NuxtLink>
    <NuxtLink to="/register">Créer un compte</NuxtLink>
  </nav>
</template>

<style scoped>

</style>