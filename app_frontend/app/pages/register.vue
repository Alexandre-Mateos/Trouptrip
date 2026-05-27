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
      passwordConfirmation: "",
      errors: {} as Record<string, string[]>,
      success: false,
    }
  },
  methods: {
    handleErrors(error: any){
      for (const violation of error.data.violations) {
        const key = violation.propertyPath;
        const message = violation.message;

        if (!this.errors[key]) {
          this.errors[key] = [];
        }

        this.errors[key].push(message);
      }
    },
    async submit() {
      this.errors = {};
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
              }
            }
        )
        this.email = "";
        this.firstname = "";
        this.lastname = "";
        this.password = "";
        this.success = true;


      } catch (error: any) {
        if(error){
          this.handleErrors(error);
        }else{
          this.errors['unexpected'] = ["Une erreure inattendue est survenue. Veuillez rééssayer"];
        }
      }
    }
  }
})
</script>

<template>
  <Header></Header>
  <h1>Créer un compte</h1>

  <ul>
    <li v-for="(message, index) in errors?.unexpected" :key="index">
      {{ message }}
    </li>
  </ul>

  <form @submit.prevent="submit">

    <div>
      <ul>
        <li v-for="(message, index) in errors?.firstname" :key="index">
          {{ message }}
        </li>
      </ul>
      <label for="firstname">Prénom :</label>
      <input type="text" id="firstname" name="firstname" v-model="firstname">
    </div>

    <div>
      <ul>
        <li v-for="(message, index) in errors?.lastname" :key="index">
          {{ message }}
        </li>
      </ul>
      <label for="lastname">Nom :</label>
      <input type="text" id="lastname" name="lastname" v-model="lastname">
    </div>

    <div>
      <ul>
        <li v-for="(message, index) in errors?.email" :key="index">
          {{ message }}
        </li>
      </ul>
      <label for="email">Adresse email :</label>
      <input id="email" name="email" v-model="email">
    </div>

    <div>
      <ul>
        <li v-for="(message, index) in errors?.plainPassword" :key="index">
          {{ message }}
        </li>
      </ul>
      <label for="password">Mot de passe :</label>
      <input id="password" name="password" v-model="password">
    </div>

    <div>
      <label for="password_confirmation">Confirmer le mot de passe :</label>
      <input id="password_confirmation" name="password_confirmation" v-model="passwordConfirmation">
    </div>

    <div>
      <button type="submit">Valider</button>
    </div>

  </form>

  <div v-if="success">
    <p>L'équipe Trouptrip te remercie pour ton inscription</p>
    <p>Un email vient d'être envoyé à l'adresse renseignée pour finaliser ton inscription</p>
  </div>
</template>

<style scoped>

</style>