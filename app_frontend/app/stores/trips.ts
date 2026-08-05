import {apiEndpoints} from "~/utils/apiEndpoints";
import type {ITripList} from "~/interfaces/trip/i-tripList";
import 'temporal-polyfill/global';
import type {ITripDetails} from "~/interfaces/trip/i-tripDetails";
import type {IMapTrip} from "~/interfaces/trip/store/i-mapTrip";
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";
import type {ITripForm} from "~/interfaces/i-tripForm";

export const useTripsStore = defineStore('trips', {
    state: () => ({
        trips: new Map<number, IMapTrip | IMapTripDetails>(),
        fetching: false,
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
                const groupItemIds = tripDetail.groupItems?.map(item => item.id) ?? [];
                const participationIds = tripDetail.participations?.map(participation => participation.id) ?? [];

                // On stock le owner et les participants à un voyage directement dans le userStore. A faire: retirer de ce Payload  pour les récupérer via un endpoint dédié.
                const userStore = useUserStore();

                userStore.tripUsers.set(tripDetail.owner.id, tripDetail.owner);

                if (tripListView) {
                    this.trips.set(tripId, {
                        ...tripListView,
                        ...tripDetail,
                        isDetail: true,
                        groupItemIds: groupItemIds,
                        personalItemIds: [],
                        participationIds: participationIds,
                    });
                } else {
                    this.trips.set(tripId, {
                        ...tripDetail,
                        isDetail: true,
                        groupItemIds: groupItemIds,
                        personalItemIds: [],
                        participationIds: participationIds,
                    });
                }

            } catch (error: any) {
                throw error;
            }
        },
        async submitTrip(body: ITripForm) {
            const { $api } = useNuxtApp();
            const url = apiEndpoints.trips;

            try {
                const response = await $api<ITripDetails>(url, {
                    method: 'POST',
                    body: {
                        title: body.tripTitle,
                        description: body.tripDescription,
                        startDate: body.tripStartDate,
                        endDate: body.tripEndDate
                    }
                });

                const groupItemIds = response.groupItems?.map((item: any) => item.id) ?? [];
                const participationIds = response.participations?.map(participation => participation.id) ?? [];

                this.trips.set(response.id, {
                    ...response,
                    isDetail: true,
                    groupItemIds: groupItemIds,
                    personalItemIds: [],
                    participationIds: participationIds,
                });
                return response;

            } catch (error: any) {
                throw error;
            }
        },
        async updateTrip(id: string, body: ITripForm) {
            const { $api } = useNuxtApp();
            const url = `${apiEndpoints.trips}/${id}`;

            try {
                const response = await $api<ITripDetails>(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/merge-patch+json'
                    },
                    body: {
                        title: body.tripTitle,
                        description: body.tripDescription,
                        startDate: body.tripStartDate,
                        endDate: body.tripEndDate
                    }
                });

                const groupItemIds = response.groupItems?.map((item: any) => item.id) ?? [];
                const participationIds = response.participations?.map(participation => participation.id) ?? [];

                this.trips.set(response.id, {
                    ...response,
                    isDetail: true,
                    groupItemIds: groupItemIds,
                    personalItemIds: [],
                    participationIds: participationIds,
                });
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