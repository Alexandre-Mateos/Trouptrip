<script lang="ts">
import {defineComponent} from 'vue'
import type {IRegisterUser} from "~/interfaces/i-registerUser";
import {apiEndpoints} from "~/utils/apiEndpoints";

export default defineComponent({
  name: "register",
  data() {
    return {
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
    handleErrors(error: any) {
      if (error?.data?.violations && Array.isArray(error.data.violations)) {
        for (const violation of error.data.violations) {
          const key = violation.propertyPath;
          const message = violation.message;

          if (!this.errors[key]) {
            this.errors[key] = [];
          }
          this.errors[key].push(message);
        }
      } else {
        this.errors['unexpected'] = ["Une erreure inattendue est survenue. Veuillez rééssayer"];
      }
    },
    async submit() {
      this.errors = {};
      this.success = false;
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
        this.handleErrors(error);
      }
    }
  }
})
</script>

<template>
  <Header></Header>
  <h1>Créer un compte</h1>

  <div v-if="errors?.unexpected">
    <ul>
      <li v-for="(message, index) in errors.unexpected" :key="index">
        {{ message }}
      </li>
    </ul>
  </div>

  <BaseForm @submit="submit">
    <FormInput id="firstname" type="text" name="firstname" v-model="firstname" label="Prénom :"
               :errors="errors?.firstname"></FormInput>
    <FormInput id="lastname" type="text" name="lastname" v-model="lastname" label="Nom :"
               :errors="errors?.lastname"></FormInput>
    <FormInput id="email" type="email" name="email" v-model="email" label="Adresse email :"
               :errors="errors?.email"></FormInput>
    <FormInput id="password" type="password" name="password" v-model="password" label="Mot de passe :"
               :errors="errors?.plainPassword"></FormInput>
    <FormInput id="password_confirmation" type="password" name="password_confirmation" v-model="passwordConfirmation"
               label="Confirmer le mot de passe :"></FormInput>
    <FormButton>Valider</FormButton>
  </BaseForm>

  <div v-if="success">
    <p>L'équipe Trouptrip te remercie pour ton inscription !</p>
    <p>Un email vient d'être envoyé à l'adresse renseignée pour finaliser ton inscription.</p>
  </div>

</template>

<style scoped>

</style>