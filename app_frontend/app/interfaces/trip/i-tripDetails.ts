import type {ITrip} from "~/interfaces/trip/i-trip";
import type {ITripOwner} from "~/interfaces/trip/i-tripOwner";

export interface ITripDetails extends ITrip{
    description: string;
    participations: {id: number}[];
    owner: ITripOwner;
    groupItems: {id: number}[]
}