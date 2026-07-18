import type {IParticipant} from "~/interfaces/i-participant";

export interface IDetailedAssignment{
    "@context": string,
    "@id": string,
    "@type": string,
    id: number,
    assignedQuantity: number,
    IsPacked: boolean,
    assignedTo: IParticipant
}