import type {IMapGroupItem} from "~/interfaces/groupItem/i-mapGroupItem";
import {apiEndpoints} from "~/utils/apiEndpoints";
import type {IGroupItemList} from "~/interfaces/groupItem/i-groupItemList";
import type {IMapTripDetails} from "~/interfaces/trip/store/i-mapTripDetails";
import type {IGroupItemRow} from "~/interfaces/groupItem/i-groupItemRow";
import type {IGroupItemForm} from "~/interfaces/groupItem/i-groupItemForm";

export const useGroupItemsStore = defineStore('groupItems', {
    state: () => ({
        groupItems: new Map<number, IMapGroupItem>(),
        fetching: false,
    }),
    getters: {
        getGroupItemListByTrip: (state) => {
            return (trip: IMapTripDetails) => {
                const storedGroupItems: IMapGroupItem[] = [];

                for (const groupItemId of trip.groupItemIds) {
                    const groupItem = state.groupItems.get(groupItemId);

                    if (groupItem !== undefined) {
                        storedGroupItems.push(groupItem);
                    }
                }

                return storedGroupItems;
            };
        }
    },
    actions: {
        async fetchGroupItems(tripId: number) {
            if (this.fetching) {
                return;
            }
            const {$api} = useNuxtApp();
            this.fetching = true;

            try {
                const groupItemCollection = await $api<IGroupItemList>(apiEndpoints.groupItemCollection(tripId));

                groupItemCollection.member.forEach((item) => {

                    const assignments = item.assignments.map((assignment) => assignment.id);

                    this.groupItems.set(item.id, {
                        ...item,
                        assignments
                    });
                });

            } catch (error: any) {
                throw error;
            } finally {
                this.fetching = false;
            }
        },
        async submitGroupItem(trip: IMapTripDetails, body: IGroupItemForm) {
            const {$api} = useNuxtApp();
            const url = apiEndpoints.groupItems;

            try {
                const response = await $api<IMapGroupItem>(url, {
                    method: 'POST',
                    body: body
                });

                this.groupItems.set(response.id, response);
                trip.groupItemIds.push(response.id);

            } catch (error: any) {
                throw error;
            }
        }
    }
})