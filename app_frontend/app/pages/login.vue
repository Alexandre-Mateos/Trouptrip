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

        navigateTo('/home');

      } catch (error: any) {
        if (error.status === 401) {
          this.errorMessage = "Votre compte n'est pas vérifié ou vos identifiants sont incorrects.";
        } else {
          this.errorMessage = "Une erreur est inattendue survenue. Veuillez réessayer";
        }
      }
    }
  }
})
</script>

<template>
  <BaseForm @submit="submit">
    <h1 class="text-2xl text-center">Se connecter</h1>
    <BaseInput id="email" type="email" name="email" v-model="email" label="Adresse email"></BaseInput>
    <BaseInput id="password" type="password" name="password" v-model="password" label="Mot de passe"></BaseInput>
    <BaseButton>Valider</BaseButton>
    <div v-if="errorMessage" class="text-center text-red-800">
      {{ errorMessage }}
    </div>
    <nav>
      <NuxtLink :to="{path: '/forgot-password', query: {email: email}}">Mot de passe oublié</NuxtLink>
    </nav>
  </BaseForm>
</template>

<style scoped>
nav{
  color: var(--color-accent);
  text-decoration: underline;
}
</style>