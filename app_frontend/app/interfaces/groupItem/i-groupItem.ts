import type {IApiResource} from "~/interfaces/i-apiResource";

export interface IGroupItem extends IApiResource{
    id: number,
    name: string,
    totalQuantity: number,
    unit: string,
    assignments: {id: number}[]
}