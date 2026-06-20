<script lang="ts">
import {defineComponent} from 'vue'
import {useTripsStore} from "~/stores/trips";

export default defineComponent({
  name: "trip",
  data() {
    return {
      windowWidth: typeof window !== 'undefined' ? window.innerWidth : 1024
    }
  },
  methods: {
    getScreenSize() {
      this.windowWidth = window.innerWidth;
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
  <p>Page des séjours</p>
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