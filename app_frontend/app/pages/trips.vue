<script lang="ts">
import { defineComponent } from 'vue'
import { useTripsStore } from "~/stores/trips";
import ActionButton from "~/components/button/ActionButton.vue";

export default defineComponent({
  name: "trip",
  components: {ActionButton},
  setup() {
    useHead({
      title: "Mes séjours",
      meta: [
        {
          name: 'description',
          content: 'Consultez la liste de vos voyages et séjours planifiés, ou organisez une nouvelle aventure.'
        }
      ]
    });
  },
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
    toggleModal() {
      this.isModalOpen = !this.isModalOpen;
    },
    focusCurrentCard() {
      const currentTripId = this.$route.params.id;
      if (!currentTripId) return;

      const tripCard = document.getElementById(`trip-card-${currentTripId}`);
      if (tripCard) {
        tripCard.focus();
      }
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
    displayStore() {
      return useDisplayStore();
    }
  },
  async mounted() {
    const tripIdParam = Number(this.$route.params.id);

    try {
      await useTripsStore().fetchTrips();
      if (this.displayStore.isDesktop && this.getFirstTripId && !tripIdParam) {
        navigateTo({ name: 'trips-id', params: { id: this.getFirstTripId } });
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
  <div class="flex flex-col gap-6 w-full py-2">

    <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold">Mes séjours</h1>
        <p class="text-gray-600 text-sm mt-1">Retrouvez tous vos voyages programmés et en cours.</p>
      </div>

      <ActionButton @click="toggleModal" icon="fa6-solid:circle-plus">
        Créer un séjour
      </ActionButton>
    </header>

    <section>
      <h2 class="sr-only">Liste de vos séjours</h2>
      <p
          v-if="isLoading"
          class="flex justify-center items-center min-h-[50vh] text-gray-600"
      >
        Chargement de vos séjours...
      </p>

      <div
          v-else-if="hasError"
          role="alert"
          class="error-container p-6 bg-red-50 border border-red-200 rounded-md text-center space-y-2"
      >
        <p class="font-medium text-red-800">Impossible de charger vos séjours pour le moment.</p>
        <p class="text-sm text-red-600">Veuillez vérifier votre connexion ou réessayer plus tard.</p>
      </div>

      <div
          v-else-if="useTripsStore().trips.size > 0"
          class="layout mt-4"
          :class="{ 'layout-mobile': !displayStore.isDesktop }"
      >
        <nav
            aria-label="Sélection d'un séjour"
            class="cards-block"
            :class="{ 'hidden-mobile': isTripViewVisible }"
        >
          <ul
              class="flex flex-col gap-2 p-0 m-0 list-none"
              aria-live="polite"
              aria-atomic="false"
          >
            <li
                v-for="trip in trips"
                :key="trip.id"
            >
              <NuxtLink
                  :id="`trip-card-${trip.id}`"
                  :to="{
                  name: 'trips-id',
                  params: { id: trip.id },
                  query: { focus: 'true' }
                }"
                  :aria-current="Number($route.params.id) === trip.id ? 'page' : undefined"
              >
                <TripCard :trip="trip"/>
              </NuxtLink>
            </li>
          </ul>
        </nav>

        <section
            aria-label="Détail du séjour"
            class="page-block"
            :class="{ 'hidden-mobile': !isTripViewVisible }"
        >
          <NuxtPage @close="focusCurrentCard" />
        </section>
      </div>

      <div v-else class="text-center py-12 bg-gray-50 border border-dashed rounded-md space-y-2">
        <p class="text-gray-600 text-lg font-medium">Vous n'avez pas encore de voyages à afficher.</p>
        <p class="text-sm text-gray-500">Commencez par en créer un grâce au bouton ci-dessus !</p>
      </div>
    </section>

    <BaseModal :open="isModalOpen">
      <TripForm @done="toggleModal"/>
    </BaseModal>

  </div>
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