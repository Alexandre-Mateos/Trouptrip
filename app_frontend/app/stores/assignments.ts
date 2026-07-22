import {apiEndpoints} from "~/utils/apiEndpoints";
import type {IAssignmentList} from "~/interfaces/assignment/i-assignmentList";
import type {IAssignment} from "~/interfaces/assignment/i-assignment";
import type {IMapGroupItem} from "~/interfaces/groupItem/i-mapGroupItem";
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
        },
        async submitAssignment(groupItem: IMapGroupItem, body: {
            assignedQuantity: number,
            groupItem: string
        }){

            const {$api} = useNuxtApp();
            const url = apiEndpoints.assignments;

            try{
                const response = await $api<IAssignment>(url,{
                    method: 'POST',
                    body: body
                });

                this.assignments.set(response.id, response);
                groupItem.assignments.push(response.id);

            } catch (error: any) {
                throw error;
            }
        },
        async updateAssignment(
            groupItem: IMapGroupItem,
            assignmentId: number,
            body: {
            assignedQuantity: number,
            groupItem: string
            }
        ){
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
        async deleteAssignment(assignmentId: number, groupItem: IMapGroupItem){
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