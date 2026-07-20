import type {IApiResource} from "~/interfaces/i-apiResource";

export interface IMapGroupItem extends IApiResource{
    id: number,
    name: string,
    totalQuantity: 1,
    unit: string,
    assignments: number[]
}