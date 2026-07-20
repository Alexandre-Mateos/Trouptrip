import type {IApiResource} from "~/interfaces/i-apiResource";

export interface ITripOwner extends IApiResource{
    id: number,
    firstname: string,
    lastname: string,
}