import type {IApiResource} from "~/interfaces/i-apiResource";

export interface IMapGroupItem extends IApiResource{
    id: number,
    name: string,
    totalQuantity: number,
    unit: string,
    assignments: number[]
}