<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  name: "verify-email",
  data(){
    return{
      success: false,
      errorMessage: ''
    }
  },
  methods: {
    async verifyEmail(token: string) {
      // On récupère "error" renvoyé par useFetch
      const { error } = await useFetch(apiEndpoints.verifyEmail, {
          method: 'POST',
          body: { token }
        });

      if (error.value) {
        this.errorMessage = "Une erreur est survenue lors de la vérification du lien.";
        return;
      }

        this.success = true;
        this.redirectToLogin();
    },
    redirectToLogin(): void
    {
      setTimeout(()=>{
        navigateTo('/login');
      }, 4000);
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
  <p v-if="errorMessage" >
    {{ errorMessage }}
  </p>
</template>

<style scoped>

</style>