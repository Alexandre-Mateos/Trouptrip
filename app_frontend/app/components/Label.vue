<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "Label",
  props: {
    dataToDisplay: {
      type: String,
      required: true
    }
  },
  methods: {
    deleteLabel() {
      this.$emit('delete', this.dataToDisplay)
    }
  }
})
</script>

<template>
  <div class="flex justify-between px-3 py-1 rounded-md label">
    <p>{{ dataToDisplay }}</p>
    <Icon name="material-symbols:delete-outline" class="text-red-700 cursor-pointer" @click="deleteLabel"></Icon>
  </div>
</template>

<style scoped>
.label{
  background-color: var(--color-bg);
  color: var(--color-text);
}
</style>