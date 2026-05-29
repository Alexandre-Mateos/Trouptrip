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
  <Header></Header>
  <h1>Se connecter</h1>

  <form @submit.prevent="submit">

    <div>
      <label for="email">Adresse email :</label>
      <input type="email" id="email" name="email" v-model="email">
    </div>

    <div>
      <label for="password">Mot de passe :</label>
      <input type="password" id="password" name="password" v-model="password">
    </div>

    <div>
      <button type="submit">Se connecter</button>
    </div>
  </form>
  <nav>
    <NuxtLink to="/forgot-password">Mot de passe oublié</NuxtLink>
  </nav>

  <div v-if="errorMessage">
    {{ errorMessage }}
  </div>
</template>

<style scoped>

</style>