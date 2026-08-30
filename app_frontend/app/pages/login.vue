<script lang="ts">
import {defineComponent} from 'vue'
import {apiEndpoints} from "~/utils/apiEndpoints";
import ActionButton from "~/components/button/ActionButton.vue";
import BaseForm from "~/components/form/BaseForm.vue";
import BaseInput from "~/components/input/BaseInput.vue";

export default defineComponent({
  name: "login",
  components: {BaseForm, ActionButton, BaseInput},
  setup() {
    useHead({
      title: 'Se connecter',
      meta: [
        {
          name: 'description',
          content: 'Accédez à votre compte TroupTrip pour retrouver vos séjours et invitations.'
        }
      ]
    })
  },
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

        await this.$api(
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
    <ActionButton type="submit">Valider</ActionButton>
    <div v-if="errorMessage" role="alert" class="text-center text-red-800">
      {{ errorMessage }}
    </div>
    <nav>
      <NuxtLink :to="{path: '/forgot-password', query: {email: email}}" class="text-trouptrip-accent-700">Mot de passe oublié</NuxtLink>
    </nav>
  </BaseForm>
</template>

<style scoped>
nav{
  color: var(--color-trouptrip-accent-500);
  text-decoration: underline;
}
</style>