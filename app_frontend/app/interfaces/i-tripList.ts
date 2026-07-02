import type {ITrip} from "~/interfaces/i-trip";

export interface ITripList {
    member: Array<ITrip>;
    totalItems?: number;
    "@context"?: string;
    "@id"?: string;
    "@type"?: string;
}