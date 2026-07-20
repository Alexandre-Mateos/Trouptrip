<script lang="ts">
import {defineComponent} from 'vue'
import type {PropType} from 'vue'

export default defineComponent({
  name: "BaseSelect",
  props: {
    label: {
      type: String,
      required: true
    },
    selectName: {
      type: String,
      required: true
    },
    modelValue: {
      type: String,
      default: '',
      required: true
    },
    selectOptions: {
      type: Array as PropType<{ value: string | number; label: string }[]>,
      required: true
    },
    errors: {
      type: Array<string>,
      required: false
    }
  },
  methods: {
    emitValue(event: Event) {
      const target = event.target as HTMLSelectElement
      this.$emit('update:modelValue', target.value)
    }
  }
})
</script>

<template>
  <label :for="selectName"></label>

  <ul v-if="errors && errors.length > 0" >
    <li v-for="(error, index) in errors" :key="index"
        class="text-xs text-red-800"
    >
      {{ error }}
    </li>
  </ul>

  <select :id="selectName"
          v-bind="$attrs"
          :value="modelValue"
          @input="emitValue($event)"
          class="rounded-md px-2"
          :class="{'border-2 border-red-800': errors && errors.length > 0}"
  >
    <option value="">--Veuillez choisir une option--</option>
    <option
        v-for="option in selectOptions"
        :key="option.value"
        :value="option.value"
    >
      {{ option.label }}
    </option>
  </select>
</template>

<style scoped>

</style>