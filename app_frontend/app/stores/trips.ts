import {apiEndpoints} from "~/utils/apiEndpoints";
import type {ITripList} from "~/interfaces/i-tripList";
import type {ITrip} from "~/interfaces/i-trip";
import 'temporal-polyfill/global'

export const useTripsStore = defineStore('trips', {
    state: () => ({
        trips: new Map<number, ITrip>(),
        fetching: false
    }),
    getters: {
        tripsList: (state) => Array.from(state.trips.values()),
        calendarDatas (state) {
            const calendarDatas = Array.from(state.trips.values());
            const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            return calendarDatas.map((trip) => {

                const startDateTime = Temporal.Instant.from(trip.startDate)
                    .toZonedDateTimeISO(userTimeZone)
                    .toPlainDate();

                const endDateTime = Temporal.Instant.from(trip.endDate)
                    .toZonedDateTimeISO(userTimeZone)
                    .toPlainDate();

                return {
                    id: trip.id,
                    title: trip.title,
                    start: startDateTime,
                    end: endDateTime
                }
            });
        }
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
            } catch (error: any) {
                this.trips.clear();
            } finally {
                this.fetching = false;
            }
        },
    }
})