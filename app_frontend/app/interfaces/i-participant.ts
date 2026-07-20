import type {IApiResource} from "~/interfaces/i-apiResource";

export interface IParticipant extends IApiResource{
    id: number,
    firstname: string
    lastname: string
}