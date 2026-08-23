<script lang="ts">
import {defineComponent} from 'vue'
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";
import type {IPersonalItem} from "~/interfaces/personalItem/I-presonalItem";
import ActionButton from "~/components/button/ActionButton.vue";
import CancelButton from "~/components/button/CancelButton.vue";

export default defineComponent({
  name: "PersonalItemForm",
  components: {CancelButton, ActionButton},
  props: {
    trip: {
      type: Object as PropType<IMapTripDetails>,
      required: true
    },
    personalItemToEdit: {
      type: Object as PropType<IPersonalItem | null>,
      default: null
    }
  },
  data() {
    return {
      personalItemName: '',
      personalItemQty: 0,
      personalItemType: '',
      errors: {} as Record<string, string[]>,
      isSubmitting: false,
      selectOptions: [
        {value: 'piece', label: 'pièce'},
        {value: 'ml', label: 'ml'},
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

      const payload = {
        name: this.personalItemName,
        quantity: this.personalItemQty,
        unit: this.personalItemType
      };

      let response;

      try {

        if(this.personalItemToEdit){
          response = await usePersonalItemsStore().updatePersonalItem(this.trip, this.personalItemToEdit.id, payload);
        }else{
          response = await usePersonalItemsStore().postPersonalItem(this.trip, payload);
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
    if(this.personalItemToEdit){
      this.personalItemName = this.personalItemToEdit.name;
      this.personalItemQty = this.personalItemToEdit.quantity;
      this.personalItemType = this.personalItemToEdit.unit;
    }
  }
})
</script>

<template>
  <BaseForm class="w-full" @submit="handleSubmit">
    <BaseInput id="personalItem_name" label="Nom" v-model="personalItemName" :errors="errors?.name"/>
    <NumberInput id="personalItem_qty" type="number" label="Quantité" min="1"
                 v-model="personalItemQty" :errors="errors?.quantity"></NumberInput>
    <BaseSelect :select-options="selectOptions" select-name="type-options" label="Type" v-model="personalItemType" :errors="errors?.unit"/>

    <div class="flex gap-2">
      <ActionButton type="submit" :disabled="isSubmitting">Valider</ActionButton>
      <CancelButton type="button" @click="$emit('done')">Annuler</CancelButton>
    </div>
  </BaseForm>
</template>

<style scoped>

</style>