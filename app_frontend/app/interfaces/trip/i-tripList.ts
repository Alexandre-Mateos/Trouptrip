import type {ITrip} from "~/interfaces/trip/i-trip";

export interface ITripList {
    member: Array<ITrip>;
    totalItems?: number;
    "@context"?: string;
    "@id"?: string;
    "@type"?: string;
}