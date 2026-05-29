<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "reset-password",
  data(){
    return{
      password: "",
      passwordConfirmation: "",
      errorMessage: "",
      success: false
    }
  },
  methods: {
    async submit(){
      const tokenFromUrl = this.$route.query.token;
      try{
        const response = this.$api(apiEndpoints.resetPassword,{
          methods: 'POST',
          body: {
            token: tokenFromUrl,
            plainPassword: this.password
          }
        })

        this.success = true;
        this.redirectToLogin();

      }catch(error: any){
        // critique: il faut savoir si le mot de passe a été modifié ou pas et affiché un message d'erreur plus précis
        this.errorMessage = "Une erreure inattendue est survenue"
      }
    },
    redirectToLogin(): void
    {
      setTimeout(()=>{
        navigateTo('/login');
      }, 4000);
    }
  }
})
</script>

<template>
  <Header></Header>
  <h1>Réinitialisation de votre mot de passe</h1>
    <form @submit.prevent="submit">
      <div>
        <label for="password">Nouveau mot de passe :</label>
        <input type="password" id="password" name="password" v-model="password">
      </div>

      <div>
        <label for="password_confirmation">Confirmer le nouveau mot de passe :</label>
        <input type="password" id="password_confirmation" name="password_confirmation" v-model="passwordConfirmation">
      </div>
    </form>

  <div>
    <p>Votre modification de mot de passe a bien été prise en compte</p>
    <p>Vous allez être redirigé vers la page de connection</p>
  </div>
</template>

<style scoped>

</style>