<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "reset-password",
  data(){
    return{
      password: "",
      passwordConfirmation: "",
      email: "",
      token: "",
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
    <BaseForm @submit="submit">

      <FormInput id="email" type="email" name="email" v-model="email" label="Email :"></FormInput>
      <OtpInputFields v-model="token" :length="6"></OtpInputFields>
      <FormInput id="password" type="password" name="password" v-model="password" label="Nouveau mot de passe :"></FormInput>
      <FormInput id="password_confirmation" type="password" name="password_confirmation" v-model="passwordConfirmation" label="Confirmer le nouveau mot de passe :"></FormInput>

    </BaseForm>

  <div>
    <p>Votre modification de mot de passe a bien été prise en compte</p>
    <p>Vous allez être redirigé vers la page de connection</p>
  </div>
</template>

<style scoped>

</style>