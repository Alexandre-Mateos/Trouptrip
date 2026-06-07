<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  name: "VerifyEmail",
  data() {
    return {
      success: false,
      errorMessage: '',
      email: '',
      token: '',
    }
  },
  methods: {
    async verifyEmail() {
      // On réinitialise l'erreur à chaque tentative
      this.errorMessage = '';

      try {
        await this.$api(apiEndpoints.verifyEmail, {
          method: 'POST',
          body: {
            email: this.email,
            token: this.token // Contient les 6 chiffres synchronisés
          }
        });

        this.success = true;
        // On lance la redirection automatique maintenant que c'est un succès
        this.redirectToLogin();

      } catch (err: any) {
        // Correction ici : on assigne à la variable de data 'this.errorMessage'
        // pour que le template HTML puisse l'afficher
        this.errorMessage = 'Une erreur inattendue est survenue, veuillez réessayer';
      }
    },
    redirectToLogin(): void {
      setTimeout(() => {
        navigateTo('/login');
      }, 4000);
    },
    handleEmailFromUrl(): void{
      const email = this.$route.query.email as string;
      if(email){
        this.email = email;
      }
    }
  },
  mounted(): any {
    this.handleEmailFromUrl();
  }
})
</script>

<template>
  <div v-if="errorMessage">
    {{ errorMessage }}
  </div>

  <div v-if="success">
    <p>Votre email a pu être vérifié avec succès, vous allez être redirigé vers la page de connexion...</p>
  </div>
  <div v-else>
    <BaseForm @submit.prevent="verifyEmail">
      <BaseInput id="email" type="email" name="email" v-model="email" label="Adresse email" required></BaseInput>
      <OtpInputFields v-model="token" :length="6"/>
      <BaseButton type="submit">Vérifier mon email</BaseButton>
    </BaseForm>
  </div>
</template>

<style scoped>

</style>