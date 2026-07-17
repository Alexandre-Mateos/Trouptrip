<script setup lang="ts">
import type { IMapTripDetails } from "~/interfaces/trip/store/i-mapTripDetails"
import Collapsible from "~/components/Collapsible.vue"

const props = defineProps<{
  trip: IMapTripDetails
}>()

const columns = [
  { accessorKey: "name", header: "A amener" },
  { accessorKey: "totalQuantity", header: "Quantité" },
  { accessorKey: "unit", header: "Unité" }
]

const groupItems = computed(() => {
  return useGroupItemsStore().getGroupItemListByTrip(props.trip)
})
</script>

<template>
  <Collapsible>
    <UTable :data="groupItems"
            :columns="columns"
            :ui="{
              thead: 'bg-surface-primary-trouptrip',
              th: 'text-trouptrip-title'
            }"
            class="flex-1 my-4"
    />
  </Collapsible>
</template>

<style scoped>

</style>