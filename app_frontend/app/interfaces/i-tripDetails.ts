import type {ITrip} from "~/interfaces/i-trip";
import type {IParticipation} from "~/interfaces/i-participation";
import type {ITripOwner} from "~/interfaces/i-tripOwner";

export interface ITripDetails {
    id: number,
    title: string,
    description: string,
    startDate: string,
    endDate: string,
    participations: IParticipation[],
    owner: ITripOwner
}