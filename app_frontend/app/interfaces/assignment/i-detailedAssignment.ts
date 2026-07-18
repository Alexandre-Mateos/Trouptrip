import type {IParticipant} from "~/interfaces/i-participant";

export interface IDetailedAssignment{
    id: number,
    assignedQuantity: number,
    IsPacked: boolean,
    assignedTo: IParticipant
}