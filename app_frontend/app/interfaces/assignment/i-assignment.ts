export interface IAssignment{
    "@context": string,
    "@id": string,
    "@type": string,
    id: number,
    assignedQuantity: number,
    IsPacked: boolean,
    assignedTo: {id: number}
}