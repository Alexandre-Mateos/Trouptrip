import {apiEndpoints} from "~/utils/apiEndpoints";
import type {IAssignmentList} from "~/interfaces/assignment/i-assignmentList";
import type {IAssignment} from "~/interfaces/assignment/i-assignment";
import type {IMapGroupItem} from "~/interfaces/groupItem/i-mapGroupItem";
import type {IDetailedAssignment} from "~/interfaces/assignment/i-detailedAssignment";
import type {IAssignedItem} from "~/interfaces/i-assignedItem";

export const useAssignmentsStore = defineStore('assignments', {
    state: () => ({
        assignments: new Map<number, IAssignment>,
        fetching: false,
        // map de type: <tripId, <userId, assignmentId[]>>
        assignmentsByUsers: new Map<number, Map<number, number[]>>(),
    }),
    getters: {
        getHandledQtyByGroupItem: (state) => {
            return (groupItem: IMapGroupItem) => {
                let handledQty: number = 0;
                groupItem.assignments.forEach((assignmentId) => {
                    const storedAssignment = state.assignments.get(assignmentId);

                    if (undefined !== storedAssignment) {
                        handledQty += storedAssignment.assignedQuantity;
                    }
                })

                if (handledQty > groupItem.totalQuantity) {
                    return groupItem.totalQuantity
                }

                return handledQty;
            }
        },
        getRemainingQtyByGroupItem() {
            return (groupItem: IMapGroupItem) => {
                return groupItem.totalQuantity - this.getHandledQtyByGroupItem(groupItem);
            }
        },
        getAssignmentsByGroupItem: (state) => {
            return (groupItem: IMapGroupItem) => {
                const storedAssignments: IDetailedAssignment[] = [];
                groupItem.assignments.forEach((groupItemId) => {
                    const assignment = state.assignments.get(groupItemId);
                    if (undefined !== assignment) {
                        const user = useUserStore().tripUsers.get(assignment.assignedTo.id);

                        if (undefined !== user) {
                            storedAssignments.push({...assignment, assignedTo: user});
                        }
                    }
                });
                return storedAssignments;
            }
        },
        getAssignedGroupItemsByUserAndTrip: (state) => {
            return (tripId: number, userId: number) => {
                const assignedItems: IAssignedItem[] = [];

                const assignmentRefs = state.assignmentsByUsers.get(tripId)?.get(userId);

                if (assignmentRefs) {
                    assignmentRefs.forEach((assignmentId) => {
                        const assignment = state.assignments.get(assignmentId);
                        if (assignment) {
                            const groupItem = useGroupItemsStore().groupItems.get(assignment.groupItem.id);
                            if (groupItem) {
                                assignedItems.push({
                                    id: groupItem.id,
                                    name: groupItem.name,
                                    unit: groupItem.unit,
                                    isPacked: assignment.isPacked,
                                    quantity: assignment.assignedQuantity,
                                });
                            }
                        }
                    });
                }
                return assignedItems;
            }
        },
        getAssignmentByGroupItemAndUser: (state) => {
            return (groupItem: IMapGroupItem, userId: number): IAssignment | undefined => {
                for (const assignmentId of groupItem.assignments) {
                    const assignment = state.assignments.get(assignmentId);

                    if (assignment && assignment.assignedTo.id === userId) {
                        return assignment;
                    }
                }
                return undefined;
            }
        },
        getAssignedGroupItemsTotalCountByUserAndTrip(){
            return (tripId: number, userId: number) => {
                const assignedGroupItems = this.getAssignedGroupItemsByUserAndTrip(tripId, userId);
                return assignedGroupItems.length;
            }
        },
        getIsPackedAssignedItemsCountByTripId(){
            return (tripId: number, userId: number) => {
                const assignedGroupItems = this.getAssignedGroupItemsByUserAndTrip(tripId, userId);
                let count = 0;
                assignedGroupItems.forEach((assignedItem) => {
                    if(assignedItem.isPacked){
                        count ++;
                    }
                });
                return count;
            }
        }
    },
    actions: {
        async fetchAssignments(tripId: number) {
            if (this.fetching) {
                return;
            }
            const {$api} = useNuxtApp();
            this.fetching = true;

            try {
                const assignmentCollection = await $api<IAssignmentList>(apiEndpoints.assignmentCollection(tripId));
                const assignmentIdByUserMap: Map<number, number[]> = new Map();

                assignmentCollection.member.forEach((assignment) => {
                    this.assignments.set(assignment.id, assignment);

                    const userId = assignment.assignedTo.id;
                    const assignmentIds = assignmentIdByUserMap.get(userId)
                    if (assignmentIds) {
                        assignmentIds.push(assignment.id);
                    } else {
                        const assignmentIds = [];
                        assignmentIds.push(assignment.id);
                        assignmentIdByUserMap.set(userId, assignmentIds);
                    }
                });

                this.assignmentsByUsers.set(tripId, assignmentIdByUserMap);

            } catch (error: any) {
                throw error;
            } finally {
                this.fetching = false;
            }
        },
        async submitAssignment(groupItem: IMapGroupItem, body: {
            assignedQuantity: number,
            groupItem: string
        }) {

            const {$api} = useNuxtApp();
            const url = apiEndpoints.assignments;

            try {
                const response = await $api<IAssignment>(url, {
                    method: 'POST',
                    body: body
                });

                this.assignments.set(response.id, response);

                const assignmentsByTripMap = this.assignmentsByUsers.get(response.tripId);
                if (assignmentsByTripMap) {
                    const assignmentsByUser = assignmentsByTripMap.get(response.assignedTo.id);
                    if (assignmentsByUser) {
                        assignmentsByUser.push(response.id);
                    } else {
                        const assignmentsByUser = [response.id];
                        assignmentsByTripMap.set(response.assignedTo.id, assignmentsByUser)
                    }
                } else {
                    const assignmentsByTripMap = new Map();
                    const assignmentsByUser = [response.id];
                    assignmentsByTripMap.set(response.assignedTo.id, assignmentsByUser)
                    this.assignmentsByUsers.set(response.tripId, assignmentsByTripMap);
                }

                groupItem.assignments.push(response.id);

            } catch (error: any) {
                throw error;
            }
        },
        async updateAssignment(
            groupItem: IMapGroupItem,
            assignmentId: number,
            body: {
                assignedQuantity?: number,
                groupItem?: string,
                isPacked?: boolean
            }
        ) {
            const {$api} = useNuxtApp();
            const url = `${apiEndpoints.assignments}/${assignmentId}`;
            try {
                const response = await $api<IAssignment>(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/merge-patch+json'
                    },
                    body: body
                });

                this.assignments.set(response.id, response);

                const index = groupItem.assignments.indexOf(assignmentId);

                if (response.assignedQuantity === 0) {
                    this.assignments.delete(assignmentId);
                    if (index !== -1) {
                        groupItem.assignments.splice(index, 1);
                    }
                } else {
                    if (index === -1) {
                        groupItem.assignments.push(assignmentId);
                    }
                }

            } catch (error: any) {
                throw error;
            }
        },
        async deleteAssignment(assignmentId: number, groupItem: IMapGroupItem) {
            const {$api} = useNuxtApp();
            const url = `${apiEndpoints.assignments}/${assignmentId}`;

            try {
                await $api(url, {
                    method: 'DELETE'
                });

                this.assignments.delete(assignmentId);

                if (groupItem.assignments.includes(assignmentId)) {
                    const index = groupItem.assignments.indexOf(assignmentId);
                    if (index !== -1) {
                        groupItem.assignments.splice(index, 1);
                    }
                }

            } catch (error: any) {
                throw error;
            }
        }
    }
})