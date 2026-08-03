<script lang="ts">
import {defineComponent} from 'vue'
import {useTripsStore} from "~/stores/trips";
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";

export default defineComponent({
  props: {
    tripToEdit: {
      type: Object as PropType<IMapTripDetails | null>,
      default: null
    }
  },
  data() {
    return {
      tripTitle: '',
      tripDescription: '',
      tripStartDate: '',
      tripEndDate: '',
      errors: {} as Record<string, string[]>,
      isSubmitting: false,
    }
  },
  methods: {
    async handleSubmit() {
      this.errors = {};
      this.isSubmitting = true;

      const payload = {
        tripTitle: this.tripTitle,
        tripDescription: this.tripDescription,
        tripStartDate: this.tripStartDate,
        tripEndDate: this.tripEndDate,
      };

      let response;

      try {
        if (this.tripToEdit && this.tripToEdit.id) {
          response = await useTripsStore().updateTrip(String(this.tripToEdit.id), payload);
        } else {
          response = await useTripsStore().submitTrip(payload);
        }

        this.$emit('done');
        if (response && response.id) {
          await navigateTo({ name: 'trips-id', params: { id: response.id } });
        }

      } catch (errors) {
        this.errors = useApiErrors().formatErrors(errors);
      } finally {
        this.isSubmitting = false;
      }
    },
    formatDateForInput(startDate: string): string {
        return startDate.split('T')[0] || '';
    }
  },
  mounted(): any {
    if (this.tripToEdit) {
      this.tripTitle = this.tripToEdit.title;
      this.tripDescription = this.tripToEdit.description;
      this.tripStartDate = this.formatDateForInput(this.tripToEdit.startDate);
      this.tripEndDate = this.formatDateForInput(this.tripToEdit.endDate);
    }
  }
});
</script>

<template>
  <BaseForm class="w-full" @submit="handleSubmit">
    <BaseInput id="trip_title" label="Titre du séjour" v-model="tripTitle" :errors="errors?.title"/>
    <BaseTextarea id="trip_description" label="Description" v-model="tripDescription"/>
    <BaseInput id="start_date" type="date" label="Date de début" v-model="tripStartDate" :errors="errors?.startDate"/>
    <BaseInput id="end_date" type="date" label="Date de fin" v-model="tripEndDate" :errors="errors?.endDate"/>

    <div class="flex gap-2">
      <ActionButton type="submit" :disabled="isSubmitting" label="Valider"></ActionButton>
      <CancelButton type="button" @click="$emit('done')"></CancelButton>
    </div>
  </BaseForm>
</template>