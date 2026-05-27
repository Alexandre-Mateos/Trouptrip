<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  name: "verify-email",
  data(){
    return{
      success: false
    }
  },
  methods: {
    async verifyEmail(token: string) {
      try {
        await useFetch(apiEndpoints.verifyEmail, {
          method: 'POST',
          body: { token }
        });

        this.success = true;

        this.redirectToLogin();
      } catch (e) {
        console.log('erreur');
      }
    },
    redirectToLogin(): void
    {
      setTimeout(()=>{
        navigateTo('/login');
        console.log('redirect');
      }, 7000);
    }
  },
  mounted() {
    const tokenFromUrl = this.$route.query.token;

    if (typeof tokenFromUrl === 'string') {
      this.verifyEmail(tokenFromUrl);
    }
  }
})
</script>

<template>
  <div v-if="success">
    <p>Votre adresse à pû être vérifié avec succès !</p>
    <p>Merci pour votre inscription</p>
    <p>Vous allez être redirigé vars la page de connection</p>
  </div>
  <div v-else>
    <p>Nous vérifions votre adresse email</p>
  </div>
</template>

<style scoped>

</style>