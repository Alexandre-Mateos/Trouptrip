<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "home",
  computed: {
    userStore() {
      return useUserStore()
    },
  },
})
</script>

<template>
<p v-if="userStore.user">Bonjour {{userStore.user.firstname}}</p>
</template>

<style scoped>

</style>