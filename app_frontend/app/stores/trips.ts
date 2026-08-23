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
        tripListIds: [] as number[],
        calendarTripIds: [] as number[],
        paginationData: {
            page: 1,
            pageItemNumber: 5,
            totalItems: null as number | null,
        }
    }),
    getters: {
        calendarDatas(state) {
            return state.calendarTripIds
                .map(id => state.trips.get(id))
                .filter(
                    (trip): trip is IMapTrip | IMapTripDetails =>
                        trip !== undefined
                )
                .map((trip) => ({
                    id: trip.id,
                    title: trip.title,
                    start: getFormatedDate(trip.startDate),
                    end: getFormatedDate(trip.endDate)
                }));
        },
        tripList(state) {
            return state.tripListIds
                .map(id => state.trips.get(id))
                .filter(
                    (trip): trip is IMapTrip | IMapTripDetails =>
                        trip !== undefined
                );
        },
        getTripById: (state) => {
            return (id: number) => state.trips.get(id);
        },
        getFirstTripId: (state) => {
            return state.tripListIds[0];
        },
        getUpcomingTrip: (state) => {
            let nextTrip = null as IMapTrip | IMapTripDetails | null;

            state.trips.forEach((trip) => {
                if (!nextTrip || trip.startDate < nextTrip.startDate) {
                    nextTrip = trip;
                }
            });

            return nextTrip;
        }
    },
    actions: {
        async fetchTrips() {
            if (this.fetching) {
                return;
            }

            const { $api } = useNuxtApp();
            this.fetching = true;
            const today = new Date().toISOString().split('T')[0];

            try {
                const tripCollection = await $api<ITripList>(
                    apiEndpoints.trips,
                    {
                        query: {
                            'endDate[gte]': today,
                            page: this.paginationData.page,
                        },
                    }
                );

                const currentPageIds: number[] = [];

                tripCollection.member.forEach((trip) => {
                    currentPageIds.push(trip.id);

                    const existingTrip = this.trips.get(trip.id);

                    if (existingTrip?.isDetail) {
                        this.trips.set(trip.id, {
                            ...existingTrip,
                            ...trip,
                            isDetail: true,
                        });
                    } else {
                        this.trips.set(trip.id, {
                            ...trip,
                            isDetail: false,
                        });
                    }
                });

                this.tripListIds = currentPageIds;

                if(tripCollection.totalItems){
                    this.paginationData.totalItems = tripCollection.totalItems;
                }

            } finally {
                this.fetching = false;
            }
        },
        async fetchCalendarTrips(
            startDate: string,
            endDate: string
        ) {
            const { $api } = useNuxtApp();

            const tripCollection = await $api<ITripList>(
                apiEndpoints.tripCalendar,
                {
                    query: {
                        'startDate[lte]': endDate,
                        'endDate[gte]': startDate,
                    },
                }
            );
            const calendarIds: number[] = [];

            tripCollection.member.forEach((trip) => {
                calendarIds.push(trip.id);

                const existingTrip = this.trips.get(trip.id);

                if (existingTrip?.isDetail) {
                    this.trips.set(trip.id, {
                        ...existingTrip,
                        ...trip,
                        isDetail: true,
                    });
                } else {
                    this.trips.set(trip.id, {
                        ...trip,
                        isDetail: false,
                    });
                }
            });

            this.calendarTripIds = calendarIds;

            return this.calendarDatas;
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

                // on refait un fetch de la page courante pour mettre à jour la pagination géré par api platform
                await this.fetchTrips();

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


                // on refait un fetch de la page courante pour mettre à jour la pagination géré par api platform
                await this.fetchTrips();

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

                // on refait un fetch de la page courante pour mettre à jour la pagination géré par api platform
                await this.fetchTrips();

            } catch (error: any) {
                throw error;
            }
        },
        removeTripFromState(tripId: number) {
            this.trips.delete(tripId);
        },
        clearStore() {
            this.trips.clear();
            this.tripListIds = [];
            this.calendarTripIds = [];
            this.fetching = false;
            this.paginationData = {
                page: 1,
                pageItemNumber: 5,
                totalItems: null,
            };
        }
    }
})

function getFormatedDate(rawDate: string) {
    const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    return Temporal.Instant.from(rawDate)
        .toZonedDateTimeISO(userTimeZone)
        .toPlainDate();
}