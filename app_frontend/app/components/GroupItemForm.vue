<script lang="ts">
import {defineComponent} from 'vue'
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";
import type {IMapGroupItem} from "~/interfaces/groupItem/i-mapGroupItem";

export default defineComponent({
  name: "GroupItemForm",
  props: {
    trip: {
      type: Object as PropType<IMapTripDetails>,
      required: true
    },
    groupItemToEdit: {
      type: Object as PropType<IMapGroupItem | null>,
      default: null
    }
  },
  data() {
    return {
      groupItemName: '',
      groupItemTotalQty: 0,
      groupItemType: '',
      errors: {} as Record<string, string[]>,
      isSubmitting: false,
      selectOptions: [
        {value: 'Unit', label: 'Unité'},
        {value: 'g', label: 'g'},
        {value: 'kg', label: 'kg'},
        {value: 'cl', label: 'cl'},
        {value: 'L', label: 'L'}
      ]
    }
  },
  methods: {
    async handleSubmit() {
      this.errors = {};
      this.isSubmitting = true;

      const body = {
        name: this.groupItemName,
        totalQuantity: this.groupItemTotalQty,
        unit: this.groupItemType,
        trip: this.trip["@id"]
      };

      let response;

      try {

        if(this.groupItemToEdit){
          response = await useGroupItemsStore().updateGroupItem(this.trip, body, this.groupItemToEdit.id);
        }else{
          response = await useGroupItemsStore().submitGroupItem(this.trip, body);
        }

        this.$emit('done');

      } catch (errors) {
        this.errors = useApiErrors().formatErrors(errors);
      } finally {
        this.isSubmitting = false;
      }
    }
  },
  mounted(): any {
    if(this.groupItemToEdit){
      this.groupItemName = this.groupItemToEdit.name;
      this.groupItemTotalQty = this.groupItemToEdit.totalQuantity;
      this.groupItemType = this.groupItemToEdit.unit;
    }
  }
});
</script>

<template>
  <BaseForm class="w-full" @submit="handleSubmit">
    <BaseInput id="groupItem_name" label="Nom" v-model="groupItemName"/>
    <NumberInput id="groupItem_totalQty" type="number" label="Quantité" min="1"
                 v-model="groupItemTotalQty"></NumberInput>
    <BaseSelect :select-options="selectOptions" select-name="type-options" label="Type" v-model="groupItemType"/>

    <div class="flex gap-2">
      <ActionButton type="submit" :disabled="isSubmitting"></ActionButton>
      <CancelButton type="button" @click="$emit('done')"></CancelButton>
    </div>
  </BaseForm>
</template>

<style scoped>

</style>