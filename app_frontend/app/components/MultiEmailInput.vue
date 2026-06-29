<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "InviteParticipantInput",
  data(){
    return{
      participantList: new Map,
      email: ''
    }
  },
  methods: {
    addParticipant(){
      const participantEmail = this.email.trim();
      if (!this.participantList.has(participantEmail)){
        this.participantList.set(participantEmail, {participantEmail});
      }
      this.email = '';
      this.$emit('addParticipant', this.participantList.values())
    },
    removeParticipant(emailToRemove: string){
      if(this.participantList.has(emailToRemove)){
        this.participantList.delete(emailToRemove);
      }
    }
  }
})
</script>

<template>
  <BaseInput id="add_participant" type="email" name="add_participant" label="Inviter des amis" v-model="email" class="flex flex-grow-1">
  </BaseInput>
  <BaseButton @click="addParticipant" type="button">
    <Icon name="fa6-solid:circle-plus"></Icon>
    Ajouter le participant
  </BaseButton>

  <div v-for="(mail, index) in participantList.values()" :key="index">
    <Label :data-to-display="mail.participantEmail" @delete="removeParticipant"></Label>
  </div>

</template>

<style scoped>

</style>