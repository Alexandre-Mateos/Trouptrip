<script lang="ts">
import {defineComponent} from 'vue'
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";
import ActionButton from "~/components/button/ActionButton.vue";
import CancelButton from "~/components/button/CancelButton.vue";

export default defineComponent({
  name: "InviteUserForm",
  components: {CancelButton, ActionButton},
  props: {
    trip:{
      type: Object as PropType<IMapTripDetails>,
      required: true
    }
  },
  data(){
    return{
      invitationEmail: '',
      isSubmitting: false,
      errors: {} as Record<string, string[]>,
    }
  },
  methods: {
    async handleSubmit(){
      this.isSubmitting = true;

      const payload = {email: this.invitationEmail};
      try{
        await useParticipationStore().inviteUser(this.trip, payload);
        this.$emit('done');
      } catch (errors) {
        this.errors = useApiErrors().formatErrors(errors);
        console.log(this.errors);
      } finally {
        this.isSubmitting = false;
      }
    }
  }
})
</script>

<template>
  <BaseForm>
    <p>Note : Troutrip étant encore en phase de développement, vous ne pouvez inviter que des membres déjà inscrits sur la plateforme.</p>
    <BaseInput id="trip_invitation" label="Email de l'utilisateur à inviter" v-model="invitationEmail" :errors="errors.email"/>
    <div class="flex gap-2">
      <ActionButton type="submit" @click="handleSubmit" :disabled="isSubmitting">Envoyer</ActionButton>
      <CancelButton type="button" @click="$emit('done')">Annuler</CancelButton>
    </div>
  </BaseForm>
</template>

<style scoped>

</style>