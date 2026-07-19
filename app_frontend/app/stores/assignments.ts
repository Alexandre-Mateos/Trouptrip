import type {IGroupItemList} from "~/interfaces/groupItem/i-groupItemList";
import {apiEndpoints} from "~/utils/apiEndpoints";
import type {IAssignmentList} from "~/interfaces/assignment/i-assignmentList";
import type {IAssignment} from "~/interfaces/assignment/i-assignment";
import type {IMapGroupItem} from "~/interfaces/groupItem/i-mapGroupItem";
import type {IGroupItemRow} from "~/interfaces/groupItem/i-groupItemRow";
import type {IDetailedAssignment} from "~/interfaces/assignment/i-detailedAssignment";

export const useAssignmentsStore = defineStore('assignments', {
    state: () => ({
        assignments: new Map<number, IAssignment>,
        fetching: false
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

                if(handledQty > groupItem.totalQuantity){
                    return groupItem.totalQuantity
                }

                return handledQty;
            }
        },
        getAssignmentsByGroupItem: (state) => {
            return (groupItem: IMapGroupItem) => {
                const storedAssignments: IDetailedAssignment[] = [];
                groupItem.assignments.forEach((groupItemId) => {
                    const assignment = state.assignments.get(groupItemId);
                    if (undefined !== assignment) {
                        const user = useUserStore().tripUsers.get(assignment.assignedTo.id);

                        if(undefined !== user){
                            storedAssignments.push({...assignment, assignedTo: user});
                        }
                    }
                });
                return storedAssignments;
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
                assignmentCollection.member.forEach((assignment) => {
                    this.assignments.set(assignment.id, assignment)
                });
            } catch (error: any) {
                throw error;
            } finally {
                this.fetching = false;
            }

        }
    }
})