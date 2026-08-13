<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: "forgot-password",
  setup() {
    useHead({
      title: "Mot de passe oublié",
      meta: [
        {
          name: 'description',
          content: 'Vous avez oublié votre mot de passe ? Saisissez votre adresse email pour recevoir un lien de réinitialisation sécurisé.'
        }
      ]
    });
  },
  data() {
    return {
      email: "",
      errorMessage: "",
      success: false
    }
  },
  methods: {
    async submit() {
      try {
        await this.$api(apiEndpoints.forgotPassword, {
          method: 'POST',
          body: { email: this.email }
        });

        this.success = true;
        this.errorMessage = "";

      } catch (error: any) {
        this.success = false;
        this.errorMessage = error.data?.message || "Une erreur inattendue est survenue, veuillez réessayer.";
      }
    }
  },
  mounted() {
    const email = this.$route.query.email as string;
    if (email) {
      this.email = email;
    }
  }
})
</script>

<template>
  <div class="flex flex-col gap-6 w-full py-2">

    <header class="text-center space-y-2">
      <h1 class="text-3xl font-bold">Vous avez oublié votre mot de passe ?</h1>
      <p class="text-gray-600">
        Pas de panique ! Remplissez ce formulaire et laissez-vous guider pour réinitialiser votre accès.
      </p>
    </header>

    <div
        v-if="success"
        class="p-6 bg-surface-primary-trouptrip border border-trouptrip-accent-200 rounded-md text-center space-y-2 max-w-md mx-auto w-full"
    >
      <p class="font-medium text-lg text-green-800">
        Demande envoyée avec succès !
      </p>
      <p class="text-sm text-gray-700">
        Si cette adresse email est associée à un compte, un message vient de vous être envoyé.
      </p>
      <p class="text-sm text-gray-700">
        Merci de consulter votre boîte de réception (et vos spams) pour finaliser la demande.
      </p>
    </div>

    <div v-else class="max-w-md mx-auto w-full">
      <p
          v-if="errorMessage"
          class="text-red-800 font-medium text-center mb-4"
      >
        {{ errorMessage }}
      </p>

      <BaseForm @submit="submit">
        <BaseInput
            id="email"
            type="email"
            name="email"
            v-model="email"
            label="Adresse email :"
            required
        />

        <BaseButton type="submit" class="mt-2">Envoyer</BaseButton>
      </BaseForm>
    </div>

  </div>
</template>

<style scoped>

</style>