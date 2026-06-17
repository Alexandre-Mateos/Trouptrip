<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "[id]",
  data(){
    return{
      id: ''
    }
  },
  mounted(): any {

    const idParam = useRoute().params.id;
    if(idParam){
      this.id = String(idParam);
    }
  }
})
</script>

<template>
  <p>Ceci est le trip numéro {{id}}</p>
</template>

<style scoped>

</style>