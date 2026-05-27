<script lang="ts">
import {defineComponent} from 'vue'
import type {IRegisterUser} from "~/interfaces/i-registerUser";
import {apiEndpoints} from "~/utils/apiEndpoints";

export default defineComponent({
  name: "register",
  data(){
    return{
      email: "",
      firstname: "",
      lastname: "",
      password: "",
      passwordConfirmation: ""
    }
  },
  methods: {
    async submit() {
      try {
        const response = await this.$api<IRegisterUser>(
            apiEndpoints.users,
            {
              method: 'POST',
              body: {
              email: this.email,
              plainPassword: this.password,
              firstname: this.firstname,
              lastname: this.lastname,
        }})

        console.log(response.firstname)

      } catch (error: any) {
        console.error(error)
      }
    }
  }
})
</script>

<template>
  <h1>Créer un compte</h1>

  <form @submit.prevent="submit">

    <div>
      <label for="firstname">Prénom :</label>
      <input type="text" id="firstname" name="firstname" v-model="firstname">
    </div>

    <div>
      <label for="lastname">Nom :</label>
      <input type="text" id="lastname" name="lastname" v-model="lastname">
    </div>

    <div>
      <label for="email">Adresse email :</label>
      <input type="email" id="email" name="email" v-model="email">
    </div>

    <div>
      <label for="password">Mot de passe :</label>
      <input type="password" id="password" name="password" v-model="password">
    </div>

    <div>
      <label for="password_confirmation">Confirmer le mot de passe :</label>
      <input type="password" id="password_confirmation" name="password_confirmation" v-model="passwordConfirmation">
    </div>

    <div>
      <button type="submit">Valider</button>
    </div>

  </form>
</template>

<style scoped>

</style>