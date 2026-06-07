<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "forgot-password",
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

        const response = await this.$api(apiEndpoints.forgotPassword, {
          method: 'POST',
          body: {email: this.email}
        });

        this.success = true;
        this.errorMessage = "";

      } catch (err: any) {
        this.success = false;

        this.errorMessage = err.data?.message || "Une erreur inattendue est survenue";
      }
    }
  },
  mounted(): any {
    const email = this.$route.query.email as string;
    if(email){
      this.email = email;
    }
  }
})
</script>

<template>
  <div class="py-8">
    <h1 class="text-2xl text-center">Vous avez oublié votre mot de passe ?</h1>
    <p class="text-center">Pas de panique</p>
    <p class="text-center">Remplissez ce formulaire et laissez vous guider !</p>
  </div>

  <BaseForm @submit="submit">
    <BaseInput id="email" type="email" name="email" v-model="email" label="Email"></BaseInput>
    <BaseButton>Envoyer</BaseButton>
  </BaseForm>

  <div v-if="errorMessage">
    <p>{{ errorMessage }}</p>
  </div>
  <div v-if="success">
    <p>Si cette adresse email est associée à un compte, un message vient de vous être envoyé.</p>
    <p>Merci de consulter votre boîte de réception pour finaliser la demande.</p>
  </div>
</template>

<style scoped>

</style>