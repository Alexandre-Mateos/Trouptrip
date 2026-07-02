import type {IParticipant} from "~/interfaces/i-participant";

export interface IParticipation{
    id: number,
    status: string,
    participant: IParticipant
}