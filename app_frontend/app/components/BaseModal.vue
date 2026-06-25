<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "BaseModal",
  inheritAttrs: false,
  methods: {
    open() {
      const dialog = this.$refs.modal as HTMLDialogElement | undefined;
      dialog?.showModal();
    },
    close() {
      const dialog = this.$refs.modal as HTMLDialogElement | undefined;
      dialog?.close();
    }
  }
})
</script>

<template>
  <dialog ref="modal" v-bind="$attrs" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box">
      <div class="modal-action">
        <slot>
        </slot>
      </div>
    </div>
  </dialog>
</template>

<style scoped>
.modal-box{
  background-color: var(--color-bg);
}
</style>