import type {IAssignment} from "~/interfaces/assignment/i-assignment";

export interface IAssignmentList{
    member: IAssignment[];
    totalItems?: number;
    "@context"?: string;
    "@id"?: string;
    "@type"?: string;
}