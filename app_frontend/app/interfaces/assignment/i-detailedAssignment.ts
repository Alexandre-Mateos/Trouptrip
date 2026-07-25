import type {IParticipant} from "~/interfaces/i-participant";
import type {IApiResource} from "~/interfaces/i-apiResource";

export interface IDetailedAssignment extends IApiResource{
    id: number,
    assignedQuantity: number,
    isPacked: boolean,
    groupItem: {id: number},
    assignedTo: IParticipant,
    tripId: number
}