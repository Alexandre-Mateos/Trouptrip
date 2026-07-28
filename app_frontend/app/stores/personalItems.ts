import type {IPersonalItem} from "~/interfaces/personalItem/I-presonalItem";
import type {IPersonalItemList} from "~/interfaces/personalItem/i-personalItemList";
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";
import type {IAssignment} from "~/interfaces/assignment/i-assignment";
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
                if(trip && trip.isDetail){
                    trip.personalItemIds.forEach((personalItemsIds) => {
                        const personalItem = state.personalItems.get(personalItemsIds);
                        if(personalItem){
                            personalItemsByTrip.push(personalItem);
                        }
                    })
                }

                return personalItemsByTrip;
            }
        }
    },
    actions: {
        async fetchPersonalItems(tripId: number){
            if (this.fetching) {
                return;
            }
            const {$api} = useNuxtApp();
            this.fetching = true;

            try{
                const personalItemCollection = await $api<IPersonalItemList>(apiEndpoints.personalItemCollection(tripId));

                const trip = useTripsStore().getTripById(tripId);

                personalItemCollection.member.forEach((personalItem) => {
                    this.personalItems.set(personalItem.id, personalItem);
                    if(trip && trip.isDetail){
                        const personalItemIds = trip.personalItemIds;
                        if(!personalItemIds.includes(personalItem.id)){
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
        }){
            if (this.fetching) {
                return;
            }
            const {$api} = useNuxtApp();
            const url = apiEndpoints.personalItems;
            this.fetching = true;

            const body = { ...payload, trip: trip["@id"] };

            try{
                const response = await $api<IPersonalItem>(url,{
                    method: 'POST',
                    body: body
                });

                this.personalItems.set(response.id, response);

                if(!trip.personalItemIds.includes(response.id)){
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
                name: string,
                quantity: number,
                unit: string
        }){

            const {$api} = useNuxtApp();
            const url = `${apiEndpoints.personalItems}/${personalItemId}`;

            try{

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
        }
    }
})