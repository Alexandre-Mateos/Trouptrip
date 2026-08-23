<script lang="ts">
import { defineComponent } from 'vue'
import ActionButton from "~/components/button/ActionButton.vue";

export default defineComponent({
  name: "reset-password",
  components: {ActionButton},
  setup() {
    useHead({
      title: 'Réinitialisation de votre mot de passe',
      meta: [
        {
          name: 'description',
          content: 'Saisissez votre code de vérification et votre nouveau mot de passe.'
        }
      ]
    })
  },
  data() {
    return {
      password: "",
      passwordConfirmation: "",
      email: "",
      token: "",
      errorMessage: "",
      success: false
    }
  },
  methods: {
    async submit() {
      this.errorMessage = "";
      const tokenFromUrl = this.$route.query.token || this.token;

      try {
        await this.$api(apiEndpoints.resetPassword, {
          method: 'POST',
          body: {
            token: tokenFromUrl,
            plainPassword: this.password
          }
        });

        this.success = true;
        this.redirectToLogin();

      } catch (error: any) {
        this.errorMessage = "Une erreur inattendue est survenue. Veuillez réessayer.";
      }
    },
    redirectToLogin(): void {
      setTimeout(() => {
        navigateTo('/login');
      }, 4000);
    }
  }
})
</script>

<template>
  <div class="flex flex-col gap-6 w-full py-2">

    <header class="text-center space-y-1">
      <h1 class="text-3xl font-bold">Réinitialisation de votre mot de passe</h1>
      <p>Choisissez un nouveau mot de passe pour votre compte.</p>
    </header>

    <div
        v-if="success"
        aria-live="polite"
        class="p-6 bg-surface-primary-trouptrip border border-trouptrip-accent-200 rounded-md text-center space-y-2"
    >
      <p class="font-medium text-lg text-green-800">Votre modification de mot de passe a bien été prise en compte !</p>
      <p class="text-sm text-gray-600">Vous allez être redirigé vers la page de connexion...</p>
    </div>

    <div v-else class="max-w-md mx-auto w-full">
      <p v-if="errorMessage" role="alert" class="error-message text-red-800 font-medium text-center mb-4">
        {{ errorMessage }}
      </p>

      <BaseForm @submit="submit">
        <BaseInput id="email" type="email" name="email" v-model="email" label="Adresse email :" />

        <OtpInputFields v-model="token" :length="6" />

        <BaseInput id="password" type="password" name="password" autocomplete="new-password" v-model="password" label="Nouveau mot de passe :" />
        <BaseInput id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" v-model="passwordConfirmation" label="Confirmer le nouveau mot de passe :" />

        <ActionButton type="submit" class="mt-2">Valider</ActionButton>
      </BaseForm>
    </div>

  </div>
</template>

<style scoped>

</style>