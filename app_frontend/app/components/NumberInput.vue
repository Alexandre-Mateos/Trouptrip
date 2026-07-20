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
        type="number"
        :value="modelValue"
        @input="emitValue($event)"
        class="rounded-md px-2"
        :class="{'border-2 border-red-800': errors && errors.length > 0}"
    >
  </div>
</template>

<style scoped>
input{
  background-color: var(--color-surface-secondary-trouptrip);
  border: solid 1px var(--color-trouptrip-accent-200);
}
</style>