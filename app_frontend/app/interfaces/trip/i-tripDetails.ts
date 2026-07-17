import type {ITrip} from "~/interfaces/trip/i-trip";
import type {IParticipation} from "~/interfaces/i-participation";
import type {ITripOwner} from "~/interfaces/trip/i-tripOwner";

export interface ITripDetails extends ITrip{
    description: string;
    participations: IParticipation[];
    owner: ITripOwner;
    groupItems: {id: number}[]
}