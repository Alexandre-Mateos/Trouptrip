<script lang="ts">
import {defineComponent} from 'vue'
import {apiEndpoints} from "~/utils/apiEndpoints";
import type {IRegisterUser} from "~/interfaces/i-registerUser";

export default defineComponent({
  name: "login",
  data(){
    return{
      email: "",
      password: "",
      errorMessage: ""
    }
  },
  methods: {
    async submit() {
      try {
        this.errorMessage = "";

        const response = await this.$api(
            apiEndpoints.login,
            {
              method: 'POST',
              body: {
                username: this.email,
                password: this.password,
              }
            }
        )
      } catch (error: any) {
        if (error.status === 401) {
          this.errorMessage = "Votre compte n'est pas vérifié ou vos identifiants sont incorrects.";
        } else {
          this.errorMessage = "Une erreur est survenue.";
        }
      }
    }
  }
})
</script>

<template>
  <h1>Se connecter</h1>

  <BaseForm @submit="submit">

    <BaseInput id="email" type="email" name="email" v-model="email" label="Adresse email :"></BaseInput>
    <BaseInput id="password" type="password" name="password" v-model="password" label="Mot de passe :"></BaseInput>
    <BaseButton>Valider</BaseButton>

  </BaseForm>
  <nav>
    <NuxtLink to="/forgot-password">Mot de passe oublié</NuxtLink>
  </nav>

  <div v-if="errorMessage">
    {{ errorMessage }}
  </div>
</template>

<style scoped>

</style>