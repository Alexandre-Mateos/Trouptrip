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
      type: Array as () => string[],
      required: false,
      default: () => []
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

    <input
        v-bind="$attrs"
        :value="modelValue"
        @input="emitValue($event)"
        class="
      bg-white
      text-trouptrip-neutral-800
      border
      border-trouptrip-neutral-300
      rounded-md
      px-2
      py-1
      focus:border-trouptrip-accent-600
    "
        :class="{
      'border-trouptrip-error-700': errors && errors.length > 0
    }"
        :aria-invalid="errors && errors.length > 0 ? 'true' : 'false'"
        :aria-describedby="errors && errors.length > 0 ? `${$attrs.id}-error` : undefined"
    >

    <ul
        v-if="errors && errors.length > 0"
        :id="`${$attrs.id}-error`"
        aria-live="polite"
        class="list-none p-0 m-0"
    >
      <li
          v-for="(error, index) in errors"
          :key="index"
          class="text-xs text-red-800 font-medium"
      >
        {{ error }}
      </li>
    </ul>
  </div>
</template>

<style scoped>
</style>