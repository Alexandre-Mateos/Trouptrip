import {apiEndpoints} from "~/utils/apiEndpoints";
import type {ITripList} from "~/interfaces/i-tripList";
import type {ITrip} from "~/interfaces/i-trip";
import 'temporal-polyfill/global';
import type {ITripDetails} from "~/interfaces/i-tripDetails";
import type {IMapTrip} from "~/interfaces/i-mapTrip";
import type {IMapTripDetails} from "~/interfaces/i-mapTripDetails";

export const useTripsStore = defineStore('trips', {
    state: () => ({
        trips: new Map<number, IMapTrip | IMapTripDetails>(),
        fetching: false,
        errors: {} as Record<string, string[]>,
    }),
    getters: {
        calendarDatas(state) {
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
        getTripById: (state) => {
            return (id: number) => state.trips.get(id);
        },
        getFirstTripId: (state) => {
            const keys = Array.from(state.trips.keys());
            return keys[0];
        }
    },
    actions: {
        async fetchTrips() {
            if (this.fetching) {
                return;
            }
            const {$api} = useNuxtApp();
            this.fetching = true;

            try {
                const tripCollection = await $api<ITripList>(apiEndpoints.trips);

                tripCollection.member.forEach((trip) => {
                    const existingTrip = this.trips.get(trip.id);
                    if (existingTrip && existingTrip.isDetail) {
                        this.trips.set(trip.id, { ...trip, ...existingTrip });
                    } else {
                        this.trips.set(trip.id, { ...trip, isDetail: false });
                    }
                });
            } catch (error: any) {
                throw error;
            } finally {
                this.fetching = false;
            }
        },
        async fetchTrip(tripId: number) {

            const tripListView = this.trips.get(tripId);

            if (tripListView && tripListView.isDetail) {
                return;
            }

            const {$api} = useNuxtApp();
            const apiUrl = apiEndpoints.trips + '/' + tripId;
            try {
                const tripDetail = await $api<ITripDetails>(apiUrl);
                if (tripListView) {
                    this.trips.set(tripId, {...tripListView, ...tripDetail, isDetail: true})
                } else {
                    this.trips.set(tripId, {...tripDetail, isDetail: true})
                }

            } catch (error: any) {
                throw error;
            }
        },
        async submitTrip(
            body: {
                tripTitle: string,
                tripDescription: string,
                tripStartDate: string,
                tripEndDate: string,
            },
            id: string | null = null,
        ) {
            const { $api } = useNuxtApp();

            let url: string = apiEndpoints.trips;
            let method: 'POST' | 'PATCH' = 'POST';

            if (id) {
                url = `${apiEndpoints.trips}/${id}`;
                method = 'PATCH';
            }

            try {
                const response = await $api<ITripDetails>(url, {
                    method: method,
                    body: {
                        title: body.tripTitle,
                        description: body.tripDescription,
                        startDate: body.tripStartDate,
                        endDate: body.tripEndDate
                    }
                });

                this.trips.set(response.id, { ...response, isDetail: true });
                return response;

            } catch (error: any) {
                throw error;
            }
        },
        async deleteTrip(tripId: number){
            const deleteUrl = `${apiEndpoints.trips}/${tripId}`;
            const { $api } = useNuxtApp();

            try{
                await $api(deleteUrl, {
                    method: 'DELETE'
                });
                this.trips.delete(tripId)

            } catch (error: any) {
                throw error;
            }
        }
    }
})

function getFormatedDate(rawDate: string) {
    const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    return Temporal.Instant.from(rawDate)
        .toZonedDateTimeISO(userTimeZone)
        .toPlainDate();
}