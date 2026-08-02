import type {IParticipant} from "~/interfaces/i-participant";
import type {IApiResource} from "~/interfaces/i-apiResource";

export interface IParticipation extends IApiResource{
    id: number,
    status: string,
    participant: IParticipant
}