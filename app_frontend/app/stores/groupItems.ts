import type {IMapGroupItem} from "~/interfaces/groupItem/i-mapGroupItem";
import {apiEndpoints} from "~/utils/apiEndpoints";
import type {IGroupItemList} from "~/interfaces/groupItem/i-groupItemList";
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";
import type {IGroupItemRow} from "~/interfaces/groupItem/i-groupItemRow";

export const useGroupItemsStore = defineStore('groupItems', {
    state: () => ({
        groupItems: new Map<number, IMapGroupItem>(),
        fetching: false,
    }),
    getters: {
        getGroupItemListByTrip: (state) => {
            return (trip: IMapTripDetails) => {
                const result: IGroupItemRow[] = [];

                for (const groupItemId of trip.groupItemIds) {
                    const groupItem = state.groupItems.get(groupItemId);

                    if (groupItem !== undefined) {
                        result.push({
                            id: groupItem.id,
                            name: groupItem.name,
                            totalQuantity: groupItem.totalQuantity,
                            unit: groupItem.unit,
                        });
                    }
                }

                return result;
            };
        }
    },
    actions: {
        async fetchGroupItems(tripId: number){
            if (this.fetching) {
                return;
            }
            const {$api} = useNuxtApp();
            this.fetching = true;

            try{
                const groupItemCollection = await $api<IGroupItemList>(apiEndpoints.groupItemCollection(tripId));

                groupItemCollection.member.forEach((item) => {

                    const assignments = item.assignments.map((assignment) => assignment.id);

                    this.groupItems.set(item.id, {
                        ...item,
                        assignments
                    });
                });

            }catch (error: any) {
                throw error;
            } finally {
                this.fetching = false;
            }
        }
    }
})