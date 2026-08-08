<script lang="ts">
import {defineComponent} from 'vue'
import {useTripsStore} from "~/stores/trips";

export default defineComponent({
  name: "trip",
  data() {
    return {
      tripTitle: '',
      tripDescription: '',
      tripStartDate: '',
      tripEndDate: '',
      errors: {} as Record<string, string[]>,
      isSubmitting: false,
      isSuccess: false,
      isModalOpen: false,
      isLoading: true,
      hasError: false
    }
  },
  methods: {
    toggleModal(){
      this.isModalOpen = !this.isModalOpen;
    }
  },
  computed: {
    isTripViewVisible(): boolean {
      if (this.$route.params.id) {
        return true;
      } else {
        return false;
      }
    },
    trips() {
      return useTripsStore().tripList;
    },
    getFirstTripId() {
      return useTripsStore().getFirstTripId;
    },
    displayStore(){
      return useDisplayStore();
    }
  },
  async mounted() {

    const tripIdParam = Number(this.$route.params.id);

    try {
      await useTripsStore().fetchTrips();
      if (this.displayStore.isDesktop && this.getFirstTripId && !tripIdParam) {
        navigateTo({name: 'trips-id', params: {id: this.getFirstTripId}});
      }
    } catch (error) {
      this.hasError = true;
    } finally {
      this.isLoading = false;
    }
  }
});
</script>

<template>
  <ActionButton type="submit" @click="toggleModal" icon="fa6-solid:circle-plus">Créer un séjour</ActionButton>

  <div v-if="useTripsStore().trips.size > 0">
    <div v-if="isLoading" class="flex justify-center items-center min-h-[50vh]">
      <p>Chargement de vos séjours...</p>
    </div>

    <div v-else-if="hasError" class="error-container">
      <p>Impossible de charger vos séjours pour le moment.</p>
      <p>Veuillez vérifier votre connexion ou réessayer plus tard.</p>
    </div>

    <div
        class="layout mt-4"
        :class="{ 'layout-mobile': !displayStore.isDesktop }"
    >
      <div
          class="cards-block flex flex-col gap-2"
          :class="{ 'hidden-mobile': isTripViewVisible }"
      >
        <NuxtLink
            v-for="trip in trips"
            :key="trip.id"
            :to="{
            name: 'trips-id',
            params: { id: trip.id },
            query: { focus: 'true' }
            }"
            :aria-current="Number($route.params.id) === trip.id ? 'page' : undefined"
        >
          <TripCard :trip="trip" />
        </NuxtLink>
      </div>

      <div
          class="page-block"
          :class="{ 'hidden-mobile': !isTripViewVisible }"
      >
        <NuxtPage />
      </div>
    </div>
  </div>
  <div v-else>
    <p>Vous n'avez pas encore de voyages à afficher</p>
  </div>

  <BaseModal :open="isModalOpen">
    <TripForm @done="toggleModal"/>
  </BaseModal>

</template>

<style scoped>
.layout {
  display: flex;
  gap: 1.5rem;

  .cards-block {
    width: 300px;
    flex-shrink: 0;
  }

  .page-block {
    flex: 1;
    width: 100%;
  }
}

.layout-mobile {
  flex-direction: column;
  align-items: center;
  gap: 0;

  .hidden-mobile {
    display: none;
  }
}
</style>