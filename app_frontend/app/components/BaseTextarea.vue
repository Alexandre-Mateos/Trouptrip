<script lang="ts">
import {defineComponent} from 'vue'
import Input from "~/components/BaseInput.vue";

export default defineComponent({
  name: "BaseTextarea",
  inheritAttrs: false,
  props: {
    label: {
      type: String,
      required: true
    },
    modelValue: {
      type: String,
      default: '',
      required: true
    }
  },
  methods: {
    emitValue(event: Event) {
      const target = event.target as HTMLTextAreaElement
      this.$emit('update:modelValue', target.value)
    }
  }
})
</script>

<template>
  <div class="flex flex-col gap-1">
    <label :for="$attrs.id as string">{{ label }}</label>

    <textarea
        v-bind="$attrs"
        :value="modelValue"
        @input="emitValue($event)"
        class="rounded-md"
    ></textarea>
  </div>
</template>

<style scoped>
textarea{
  background-color: var(--color-bg);
}
</style>