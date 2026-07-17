export interface IGroupItem{
    "@id": string,
    "@type": string,
    id: number,
    name: string,
    totalQuantity: 1,
    unit: string,
    assignments: {id: number}[]
}