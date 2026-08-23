<script lang="ts">
import { defineComponent } from 'vue'
import type { PropType } from 'vue'

export default defineComponent({
  name: "NumberInput",
  inheritAttrs: false,
  props: {
    label: {
      type: String,
      required: true
    },
    modelValue: {
      type: [Number, Object] as PropType<number | null>,
      required: true
    },
    errors: {
      type: Array as PropType<string[]>,
      required: false
    }
  },
  methods: {
    emitValue(event: Event) {
      const target = event.target as HTMLInputElement
      this.$emit('update:modelValue', Number(target.value))
    }
  }
})
</script>

<template>
  <div class="flex flex-col gap-1 w-full">
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
        type="number"
        :value="modelValue"
        @input="emitValue($event)"
        class="
            bg-white
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
    >
  </div>
</template>

<style scoped>
</style>