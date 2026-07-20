import type {ITrip} from "~/interfaces/trip/i-trip";
import type {IApiResource} from "~/interfaces/i-apiResource";

export interface ITripList extends IApiResource{
    member: Array<ITrip>;
    totalItems?: number;
    "@context"?: string;
}