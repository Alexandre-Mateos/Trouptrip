import {apiEndpoints} from "~/utils/apiEndpoints";
import type {ITripList} from "~/interfaces/i-tripList";
import type {ITrip} from "~/interfaces/i-trip";

export const useTripsStore = defineStore('trips', {
    state: () => ({
        trips: new Map<number, ITrip>(),
        fetching: false
    }),
    getters: {
        tripsList: (state) => Array.from(state.trips.values())
    },
    actions: {
        async fetchTrips() {
            if (this.fetching) {
                return;
            }
            const { $api } = useNuxtApp();
            this.fetching = true;

            try {
                const tripCollection = await $api<ITripList>(apiEndpoints.trips);
                tripCollection.member.forEach((trip)=>{
                   this.trips.set(trip.id, trip)
                });
                console.log(tripCollection);
            } catch (error: any) {
                this.trips.clear();
            } finally {
                this.fetching = false;
            }
        },
    }
})