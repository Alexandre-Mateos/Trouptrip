import type {IApiResource} from "~/interfaces/i-apiResource";

export interface IPersonalItem extends IApiResource{
    id: number,
    name: string,
    quantity: number,
    isPacked: boolean,
    trip: { "@context": string,
            "@id": string,
            "@type": string,
            id: number
            },
    unit: string
}