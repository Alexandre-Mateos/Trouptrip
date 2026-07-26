import type {IPersonalItem} from "~/interfaces/personalItem/I-presonalItem";
import type {IPersonalItemList} from "~/interfaces/personalItem/i-personalItemList";

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
        }
    }
})