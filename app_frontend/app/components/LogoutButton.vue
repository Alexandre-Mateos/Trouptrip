<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "LogoutButton",
  methods:{
    logout(){
      try{
        useUserStore().logout();
      }catch(e){
        console.log(e);
      }
    }
  },
})
</script>

<template>
  <button type="button" @click="logout" v-bind="$attrs" class="cursor-pointer">
    Déconnexion
  </button>
</template>

<style scoped>

</style>