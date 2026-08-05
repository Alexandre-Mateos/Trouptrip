<script lang="ts">
import {defineComponent} from 'vue'
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";

export default defineComponent({
  name: "InviteUserForm",
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
    <BaseInput id="trip_invitation" label="Email de l'utilisateur à inviter" v-model="invitationEmail" :errors="errors.email"/>
    <div class="flex gap-2">
      <ActionButton type="submit" @click="handleSubmit" :disabled="isSubmitting" label="Envoyer"></ActionButton>
      <CancelButton type="button" @click="$emit('done')"></CancelButton>
    </div>
  </BaseForm>
</template>

<style scoped>

</style>