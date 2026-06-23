<script lang="ts">
import {defineComponent} from 'vue'
import {useTripsStore} from "~/stores/trips";

export default defineComponent({
  name: "trip",
  data() {
    return {
      windowWidth: typeof window !== 'undefined' ? window.innerWidth : 1024,
      tripTitle: '',
      tripDescription: '',
      tripStartDate: '',
      tripEndDate: '',
      errors: {} as Record<string, string[]>,
      isSubmitting: false,
      isSuccess: false,
    }
  },
  methods: {
    getScreenSize() {
      this.windowWidth = window.innerWidth;
    },
    openModal() {
      const modal = this.$refs.createTripModal as any;
      if (modal) {
        modal.open();
      }
    },
    closeModal() {
      const modal = this.$refs.createTripModal as any;
      if (modal) {
        modal.close();
      }
    },
    async handleSubmit() {
      this.errors = {};
      this.isSubmitting = true;

      const payload = {
        tripTitle: this.tripTitle,
        tripDescription: this.tripDescription,
        tripStartDate: this.tripStartDate,
        tripEndDate: this.tripEndDate,
      };

      try{
        const response = await useTripsStore().submitTrip(payload);
        this.isSuccess = true;
        this.closeModal();
        navigateTo({name: 'trips-id', params: { id: response.id}})
      }catch (errors){
        this.handleErrors(errors);
      }

      this.isSubmitting = false;
    },
    handleErrors(error: any) {
      if (error?.data?.violations && Array.isArray(error.data.violations)) {
        for (const violation of error.data.violations) {
          const key = violation.propertyPath;
          const message = violation.message;

          if (!this.errors[key]) {
            this.errors[key] = [];
          }
          this.errors[key].push(message);
        }
      } else {
        this.errors['unexpected'] = ["Une erreur inattendue est survenue. Veuillez réessayer."];
      }
    }
  },
  computed: {
    isDesktop(): boolean {
      return this.windowWidth > 768;
    },
    isTripViewVisible(): boolean {
      if (this.$route.params.id) {
        return true;
      } else {
        return false;
      }
    },
    trips() {
      return useTripsStore().tripList;
    }
  },
  mounted() {
    useTripsStore().fetchTrips();
    window.addEventListener('resize', this.getScreenSize);
  },
  unmounted() {
    window.removeEventListener('resize', this.getScreenSize);
  }
});
</script>

<template>

  <BaseButton @click="openModal">
    <Icon name="fa6-solid:circle-plus" ></Icon>
    Créer un séjour
  </BaseButton>

  <div :class="{'desktopStyle': isDesktop, 'mobilStyle': !isDesktop}" class="trip-container">

    <div class="tripList" :class="{'hidden': isTripViewVisible && !isDesktop}">
      <NuxtLink v-for="trip in trips" :key="trip.id" :to="{name: 'trips-id', params: { id: trip.id}}">
        <TripCard :trip="trip"></TripCard>
      </NuxtLink>
    </div>

    <div class="tripView" :class="{'hidden': !isTripViewVisible && !isDesktop}">
      <NuxtPage/>
    </div>
  </div>

  <BaseModal ref="createTripModal">
    <BaseForm class="w-full">
      <BaseInput id="trip_title" type="text" name="trip_title"
                 label="Titre du séjour" v-model="tripTitle" :errors="errors?.title"></BaseInput>
      <BaseTextarea id="trip_description" type="text" name="trip_description" label="Description du séjour" v-model="tripDescription"></BaseTextarea>
      <BaseInput id="start_date" type="date" name="start_date" label="Date de début" v-model="tripStartDate" :errors="errors?.startDate"></BaseInput>
      <BaseInput id="end_date" type="date" name="end_date" label="Date de fin" v-model="tripEndDate" :errors="errors?.endDate"></BaseInput>

      <BaseButton type="submit" @click="handleSubmit">Valider</BaseButton>
      <BaseButton type="button" :disable="isSubmitting" @click="closeModal">Annuler</BaseButton>
    </BaseForm>
  </BaseModal>

</template>

<style scoped>
.trip-container {
  min-height: 100vh;
}

.mobilStyle {
  .tripList {
    max-width: 450px;
  }
}

.desktopStyle {
  display: flex;

  .tripList {
    flex: 0 0 23%;
    min-width: 300px;
    max-width: 450px;
  }

  .tripView {
    flex-grow: 1;
  }
}
</style>