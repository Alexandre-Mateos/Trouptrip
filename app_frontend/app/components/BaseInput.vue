<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: "Input",
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
    },
    errors: {
      type: Array<string>,
      required: false
    }
  },
  methods: {
    emitValue(event: Event) {
      const target = event.target as HTMLInputElement
      this.$emit('update:modelValue', target.value)
    }
  }
})
</script>

<template>
  <div class="flex flex-col gap-1">
    <label :for="$attrs.id as string">{{ label }}</label>

    <ul v-if="errors && errors.length > 0" >
      <li v-for="(error, index) in errors" :key="index"
      class="text-xs text-red-800"
      >
        {{ error }}
      </li>
    </ul>

    <input
        v-bind="$attrs"
        :value="modelValue"
        @input="emitValue($event)"
        class="rounded-md"
        :class="{'border-2 border-red-800': errors && errors.length > 0}"
    >
  </div>
</template>

<style scoped>
input{
  background-color: var(--color-bg);
}
</style>