import type {IApiResource} from "~/interfaces/i-apiResource";

export interface IMapParticipation extends IApiResource{
    id: number,
    status: string,
    participantId: number
}