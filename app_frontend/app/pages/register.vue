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

        this.success = true;
        this.redirectToVerifyEmail();

      } catch (error: any) {
        this.handleErrors(error);
      }
    },
    redirectToVerifyEmail(): void{
      setTimeout(() => {
        navigateTo(
            {path: '/verify-email',
            query : {
              email: this.email
            }}
        );
      }, 6000);
    }
  }
})
</script>

<template>

  <div v-if="success">
    <p>Inscription réussie !</p>
    <p>Un email de confirmation vous a été envoyé pour finaliser votre inscription.</p>
    <p>Vous allez être redirigé vers la page de vérification...</p>
  </div>

  <div v-else>

    <BaseForm @submit="submit">

      <h1 class="text-2xl text-center">Créer un compte</h1>

      <BaseInput id="firstname" type="text" name="firstname" v-model="firstname" label="Prénom"
                 :errors="errors?.firstname"></BaseInput>
      <BaseInput id="lastname" type="text" name="lastname" v-model="lastname" label="Nom"
                 :errors="errors?.lastname"></BaseInput>
      <BaseInput id="email" type="email" name="email" v-model="email" label="Adresse email"
                 :errors="errors?.email"></BaseInput>
      <BaseInput id="password" type="password" name="password" autocomplete="new-password" v-model="password"
                 label="Mot de passe"
                 :errors="errors?.plainPassword"></BaseInput>
      <BaseInput id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                 v-model="passwordConfirmation"
                 label="Confirmer le mot de passe"></BaseInput>
      <BaseButton type="submit">Valider</BaseButton>

      <div v-if="errors?.unexpected">
        <ul>
          <li v-for="(message, index) in errors.unexpected" :key="index">
            {{ message }}
          </li>
        </ul>
      </div>
    </BaseForm>
  </div>

</template>

<style scoped>

</style>