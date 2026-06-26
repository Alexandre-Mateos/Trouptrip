<script lang="ts">
import { defineComponent } from 'vue'

export default defineComponent({
  name: "BaseModal",
  inheritAttrs: false,
  props: {
    open: {
      type: Boolean,
      default: false
    }
  }
})
</script>

<template>
  <Transition name="modal-fade">
  <div
      v-bind="$attrs"
      v-if="open"
      class="modal modal-open modal-bottom sm:modal-middle"
  >
    <div class="modal-box">
      <slot></slot>
    </div>
  </div>
  </Transition>
</template>

<style scoped>
.modal-box {
  background-color: var(--color-bg);
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease-in-out;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>