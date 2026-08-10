<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: "OtpInputFields",
  emits: ['update:modelValue'],
  props: {
    length: {
      type: Number,
      default: 6
    }
  },
  data() {
    return {
      fieldValues: Array(this.length).fill(''),
      isValidTokenFormat: false
    }
  },
  methods: {
    emitValue() {
      const token = this.fieldValues.join('').trim();
      this.$emit('update:modelValue', token);
    },

    handleInput(index: number) {
      this.focusNextInput(index);
      this.emitValue();
    },

    handleDelete(index: number) {
      this.focusPreviousInput(index);
      if (index > 0) {
        this.fieldValues[index - 1] = '';
      }
      this.emitValue();
    },

    handlePaste(event: ClipboardEvent) {
      const pastedToken = event.clipboardData?.getData('text');
      if (!pastedToken) return;

      const arrayToken = pastedToken.split('');
      if (arrayToken.length > this.length) return;

      arrayToken.forEach((carac, i) => {
        this.fieldValues[i] = carac;
      });

      this.emitValue();
    },
    focusFirstInput() {
      const inputs = this.$refs.inputs as HTMLInputElement[];
      const firstInput = inputs?.[0] as HTMLInputElement;
      if (firstInput) {
        firstInput.focus();
      }
    },
    focusNextInput(index: number) {
      const inputs = this.$refs.inputs as HTMLInputElement[];
      if (this.fieldValues[index] !== '' && (index + 1) < this.length) {
        const nextInput = inputs[index + 1];
        if (nextInput) {
          nextInput.focus();
        }
      }
    },
    focusPreviousInput(index: number) {
      const inputs = this.$refs.inputs as HTMLInputElement[];
      if ((index - 1) >= 0) {
        const previousInput = inputs[index - 1];
        if (previousInput) {
          previousInput.focus();
        }
      }
    }
  },
  mounted() {
    this.focusFirstInput();
  }
})
</script>

<template>
  <div class="flex flex-col gap-1">
    <label id="otp-label" class="font-medium">Code de vérification</label>

    <div
        class="flex flex-row justify-between gap-2"
        role="group"
        aria-labelledby="otp-label"
    >
      <input
          v-for="(digit, index) in length"
          :key="index"
          :id="`otp-input-${index}`"
          type="text"
          inputmode="numeric"
          pattern="[0-9]*"
          maxlength="1"
          autocomplete="one-time-code"
          :aria-label="`Chiffre ${index + 1} du code de vérification`"
          ref="inputs"
          v-model="fieldValues[index]"
          @input="handleInput(index)"
          @keyup.delete="handleDelete(index)"
          @paste="handlePaste($event)"
          class="rounded-md w-12 h-12 text-center text-lg font-bold border border-trouptrip-accent-200"
      />
    </div>
  </div>
</template>

<style scoped>
input {
  background-color: var(--color-surface-secondary-trouptrip, #f9fafb);
}
</style>