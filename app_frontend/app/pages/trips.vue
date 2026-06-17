<script lang="ts">
import {defineComponent} from 'vue'
import {useTripsStore} from "~/stores/trips";

export default defineComponent({
  name: "trip",
  data() {
    return {
      route: useRoute(),
      tripStore: useTripsStore(),
      windowWidth: typeof window !== 'undefined' ? window.innerWidth : 1024
    }
  },
  methods: {
   getScreenSize(){
     this.windowWidth = window.innerWidth;
   }
  },
  computed: {
    isDesktop() {
      const mobilWidth = 768;
      return this.windowWidth > mobilWidth;
    },
    isTripViewVisible(){
      if(this.route.params.id){
        return true;
      }
      return false;
    }
  },
  async mounted() {
    await this.tripStore.fetchTrips();
    window.addEventListener('resize', () => {
      this.getScreenSize();
    })
  }

})
</script>

<template>
  <p>Page des séjours</p>
  <div :class="{'desktopStyle': isDesktop, 'mobilStyle': !isDesktop}" class="trip-container">
    <div class="tripList" :class="{'hidden': isTripViewVisible && !isDesktop}">
      <template v-for="(trip, index) in tripStore.tripList">
        <TripCard :trip="trip"></TripCard>
      </template>
    </div>
    <div class="tripView" :class="{'hidden': !isTripViewVisible && !isDesktop}">
      <NuxtPage/>
    </div>
</div>

</template>

<style scoped>

.trip-container{
  min-height: 100vh;
}

.mobilStyle{
  .tripList{
    max-width: 450px;
  }
}

.desktopStyle{
  display: flex;

  .tripList{
    flex: 0 0 23%;
    min-width: 300px;
    max-width: 450px;
  }
  .tripView{
    flex-grow: 1;
  }
}
</style>