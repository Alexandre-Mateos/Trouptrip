import {apiEndpoints} from "~/utils/apiEndpoints";
import type {ITripList} from "~/interfaces/i-tripList";
import type {ITrip} from "~/interfaces/i-trip";
import 'temporal-polyfill/global';

export const useTripsStore = defineStore('trips', {
    state: () => ({
        trips: new Map<number, ITrip>(),
        fetching: false
    }),
    getters: {
        calendarDatas (state) {
            const calendarDatas = Array.from(state.trips.values());

            return calendarDatas.map((trip) => {

                const startDateTime = getFormatedDate(trip.startDate);
                const endDateTime = getFormatedDate(trip.endDate);

                return {
                    id: trip.id,
                    title: trip.title,
                    start: startDateTime,
                    end: endDateTime
                }
            });
        },
        tripList: (state) => Array.from(state.trips.values()),
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
        }
    }
})

function getFormatedDate(rawDate: string){
    const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    return  Temporal.Instant.from(rawDate)
        .toZonedDateTimeISO(userTimeZone)
        .toPlainDate();
}