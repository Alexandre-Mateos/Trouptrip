<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "forgot-password",
  data(){
    return{
      email: "",
      errorMessage: "",
      success: false
    }
  },
  methods: {
    async submit(){
      try {

        const response = await this.$api(apiEndpoints.forgotPassword, {
          method: 'POST',
          body: { email: this.email }
        });

        this.success = true;
        this.errorMessage = "";

      } catch (err: any) {
        this.success = false;

        this.errorMessage = err.data?.message || "Une erreur inattendue est survenue";
      }
    }
  }
})
</script>

<template>
  <Header></Header>
  <h1>Mot de passe oublié ?</h1>
  <form @submit.prevent="submit">
    <div>
      <label for="email">Adresse email :</label>
      <input type="email" id="email" name="email" v-model="email">
    </div>
  </form>
  <div v-if="errorMessage">
    <p>{{errorMessage}}</p>
  </div>
  <div v-if="success">
    <p>Nous avons pris en compte votre demande</p>
    <p>Veuillez vérifier votre boîte mail pour réinitialiser votre mot de passe</p>
  </div>
</template>

<style scoped>

</style>