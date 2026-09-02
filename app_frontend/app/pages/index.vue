<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "index.vue",
  setup() {
    useHead({
      title: 'TroupTrip',
      meta: [
        {
          name: 'description',
          content: 'Simplifiez l\'organisation de vos séjours en groupe avec TroupTrip'
        }
      ]
    })
  },
})
</script>

<template>
  <h1>Bienvenue sur Trouptrip</h1>
  <p>L'application pour organiser vos voyages en groupe.</p>
  <p>Attention, ceci est une version en cours de développement. Certaines fonctionnalités peuvent ne pas être encore disponibles</p>
</template>

<style scoped>

</style>