import type {IPersonalItem} from "~/interfaces/personalItem/I-presonalItem";
import type {IPersonalItemList} from "~/interfaces/personalItem/i-personalItemList";
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";
import {apiEndpoints} from "~/utils/apiEndpoints";

export const usePersonalItemsStore = defineStore('personalItem', {
    state: () => ({
        personalItems: new Map<number, IPersonalItem>,
        fetching: false
    }),
    getters: {
        getPersonalItemsByTripId: (state) => {
            return (tripId: number) => {
                const trip = useTripsStore().getTripById(tripId);
                const personalItemsByTrip: IPersonalItem[] = [];
                if (trip && trip.isDetail) {
                    trip.personalItemIds.forEach((personalItemsIds) => {
                        const personalItem = state.personalItems.get(personalItemsIds);
                        if (personalItem) {
                            personalItemsByTrip.push(personalItem);
                        }
                    })
                }

                return personalItemsByTrip;
            }
        },
        getPersonalItemTotalCountByTripId(){
            return (tripId: number) => {
                const personalItems = this.getPersonalItemsByTripId(tripId);
                if(!personalItems || personalItems.length === 0){
                    return 0;
                }
                return personalItems.length;
            }
        },
        getIsPackedPersonalItemsCountByTripId(){
            return (tripId: number) => {
                const personalItems = this.getPersonalItemsByTripId(tripId);
                let count = 0;
                if(personalItems && personalItems.length > 0){
                    personalItems.forEach((personalItem) => {
                        if(personalItem.isPacked){
                            count++;
                        }
                    })
                }
                return count;
            }
        }
    },
    actions: {
        async fetchPersonalItems(tripId: number) {
            if (this.fetching) {
                return;
            }
            const {$api} = useNuxtApp();
            this.fetching = true;

            try {
                const personalItemCollection = await $api<IPersonalItemList>(apiEndpoints.personalItemCollection(tripId));

                const trip = useTripsStore().getTripById(tripId);

                personalItemCollection.member.forEach((personalItem) => {
                    this.personalItems.set(personalItem.id, personalItem);
                    if (trip && trip.isDetail) {
                        const personalItemIds = trip.personalItemIds;
                        if (!personalItemIds.includes(personalItem.id)) {
                            trip.personalItemIds.push(personalItem.id);
                        }
                    }
                })

            } catch (error: any) {
                throw error;
            } finally {
                this.fetching = false;
            }
        },
        async postPersonalItem(trip: IMapTripDetails, payload: {
            name: string,
            quantity: number,
            unit: string
        }) {
            if (this.fetching) {
                return;
            }
            const {$api} = useNuxtApp();
            const url = apiEndpoints.personalItems;
            this.fetching = true;

            const body = {...payload, trip: trip["@id"]};

            try {
                const response = await $api<IPersonalItem>(url, {
                    method: 'POST',
                    body: body
                });

                this.personalItems.set(response.id, response);

                if (!trip.personalItemIds.includes(response.id)) {
                    trip.personalItemIds.push(response.id);
                }

            } catch (error: any) {
                throw error;
            } finally {
                this.fetching = false;
            }
        },
        async updatePersonalItem(
            trip: IMapTripDetails,
            personalItemId: number,
            body: {
                name?: string,
                quantity?: number,
                unit?: string,
                isPacked?: boolean
            }) {

            const {$api} = useNuxtApp();
            const url = `${apiEndpoints.personalItems}/${personalItemId}`;

            try {
                const response = await $api<IPersonalItem>(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/merge-patch+json'
                    },
                    body: body
                });
                this.personalItems.set(response.id, response);
            } catch (error: any) {
                throw error;
            }
        },
        async deletePersonalItem(
            trip: IMapTripDetails,
            personalItemId: number
        ) {
            const {$api} = useNuxtApp();
            const url = `${apiEndpoints.personalItems}/${personalItemId}`;

            try {
                await $api(url, {
                    method: 'DELETE'
                });

                this.personalItems.delete(personalItemId);

                if (trip.personalItemIds.includes(personalItemId)) {
                    const index = trip.personalItemIds.indexOf(personalItemId);
                    if (index !== -1) {
                        trip.personalItemIds.splice(index, 1);
                    }
                }

            } catch (error: any) {
                throw error;
            }
        },
        clearStore(){
            this.personalItems.clear();
            this.fetching = false;
        }
    }
})