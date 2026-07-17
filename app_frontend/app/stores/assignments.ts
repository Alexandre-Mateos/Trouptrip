import type {IGroupItemList} from "~/interfaces/groupItem/i-groupItemList";
import {apiEndpoints} from "~/utils/apiEndpoints";
import type {IAssignmentList} from "~/interfaces/assignment/i-assignmentList";
import type {IAssignment} from "~/interfaces/assignment/i-assignment";

export const useAssignmentsStore = defineStore('assignments', {
    state: () => ({
        assignments: new Map<number,IAssignment>,
        fetching: false
    }),
    actions: {
        async fetchAssignments(tripId: number) {
            if (this.fetching) {
                return;
            }
            const {$api} = useNuxtApp();
            this.fetching = true;

            try{
                const assignmentCollection = await $api<IAssignmentList>(apiEndpoints.assignmentCollection(tripId));
                assignmentCollection.member.forEach((assignment) => {
                    this.assignments.set(assignment.id, assignment)
                });
                console.log(this.assignments);
            }catch (error: any) {
                throw error;
            } finally {
                this.fetching = false;
            }

        }
    }
})