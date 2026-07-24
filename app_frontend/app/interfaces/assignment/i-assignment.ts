import type {IApiResource} from "~/interfaces/i-apiResource";

export interface IAssignment extends IApiResource{
    id: number,
    assignedQuantity: number,
    IsPacked: boolean,
    groupItem: {id: number}
    assignedTo: {id: number},
    tripId: number
}