<script lang="ts">
import {defineComponent} from 'vue'
import type {IAssignedItem} from "~/interfaces/i-assignedItem";
import type {IPersonalItem} from "~/interfaces/personalItem/I-presonalItem";

export default defineComponent({
  name: "ItemCard",
  props: {
    item: {
      type: Object as PropType<IAssignedItem|IPersonalItem>,
      required: true
    },
    isFromGroupItem: {
      type: Boolean,
      default: false
    }
  }
})
</script>

<template>
  <UCard
      :ui="{
        root: 'bg-white shadow-md transform border border-solid border-trouptrip-accent-500 ring-0'
      }"
  >
    <div class="flex justify-between">

      <div class="flex gap-2 items-center">
        <Icon
            v-if="item.isPacked"
            name="tabler:circle-check"
            class="text-trouptrip-success-500 text-xl fill-trouptrip-success-500"
        ></Icon>
        <Icon
            v-else
            name="material-symbols:circle-outline"
            class="text-trouptrip-error-500 text-xl"
        ></Icon>
        <span>{{ item.name }}</span>
      </div>

      <div class="flex items-center gap-1">
        <p>{{ item.quantity }}</p>
        <p class="text-xs">{{ item.unit }}</p>
      </div>
    </div>
    <p class="text-xs text-trouptrip-neutral-500" v-if="isFromGroupItem">Pour le groupe</p>
    <slot>

    </slot>
  </UCard>
</template>

<style scoped>

</style>