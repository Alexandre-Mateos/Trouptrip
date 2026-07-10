import type {IMapTrip} from "~/interfaces/i-mapTrip";
import type {ITripDetails} from "~/interfaces/i-tripDetails";

export interface IMapTripDetails extends ITripDetails{
    isDetail: true
}