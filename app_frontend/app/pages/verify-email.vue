<script lang="ts">
import { defineComponent } from 'vue';
import ActionButton from "~/components/button/ActionButton.vue";
import BaseForm from "~/components/form/BaseForm.vue";

export default defineComponent({
  name: "VerifyEmail",
  components: {BaseForm, ActionButton},
  setup() {
    useHead({
      title: "Vérification de l'email",
      meta: [
        {
          name: 'description',
          content: 'Saisissez votre code de vérification pour valider votre adresse email et finaliser votre inscription sur TroupTrip.'
        }
      ]
    })
  },
  data() {
    return {
      success: false,
      errorMessage: '',
      email: '',
      token: '',
    }
  },
  methods: {
    async verifyEmail() {
      // On réinitialise l'erreur à chaque tentative
      this.errorMessage = '';

      try {
        await this.$api(apiEndpoints.verifyEmail, {
          method: 'POST',
          body: {
            email: this.email,
            token: this.token
          }
        });

        this.success = true;
        this.redirectToLogin();

      } catch (err: any) {
        this.errorMessage = 'Une erreur inattendue est survenue, veuillez réessayer.';
      }
    },
    redirectToLogin(): void {
      setTimeout(() => {
        navigateTo('/login');
      }, 4000);
    },
    handleEmailFromUrl(): void {
      const email = this.$route.query.email as string;
      if (email) {
        this.email = email;
      }
    }
  },
  mounted() {
    this.handleEmailFromUrl();
  }
})
</script>

<template>
  <div class="flex flex-col gap-6 w-full py-2">

    <header class="text-center space-y-1">
      <h1 class="text-3xl font-bold">Vérification de votre adresse email</h1>
    </header>

    <div
        v-if="success"
        aria-live="polite"
        class="p-6 bg-surface-primary-trouptrip border border-trouptrip-accent-200 rounded-md text-center space-y-2 max-w-md mx-auto w-full"
    >
      <p class="font-medium text-lg text-green-800">
        Votre email a été vérifié avec succès !
      </p>
      <p class="text-sm text-gray-600">
        Vous allez être redirigé vers la page de connexion...
      </p>
    </div>

    <div v-else class="max-w-md mx-auto w-full">
      <p v-if="errorMessage" role="alert" class="error-message text-red-800 font-medium text-center mb-4">
        {{ errorMessage }}
      </p>

      <BaseForm @submit="verifyEmail">
        <BaseInput
            id="email"
            type="email"
            name="email"
            v-model="email"
            label="Adresse email :"
            required
        />

        <OtpInputFields v-model="token" :length="6" />

        <ActionButton type="submit" class="mt-2">Vérifier mon email</ActionButton>
      </BaseForm>
    </div>

  </div>
</template>

<style scoped>

</style>