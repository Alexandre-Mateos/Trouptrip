<script lang="ts">
import {defineComponent} from 'vue'

export default defineComponent({
  name: "BaseModal",
  inheritAttrs: false,
  methods: {
    open() {
      (this.$refs.modal as HTMLDialogElement).showModal()
    },

    close() {
      (this.$refs.modal as HTMLDialogElement).close()
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