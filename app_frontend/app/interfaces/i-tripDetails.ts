import type {ITrip} from "~/interfaces/i-trip";
import type {IParticipation} from "~/interfaces/i-participation";

export interface ITripDetails {
    id: number,
    title: string,
    description: string,
    startDate: string,
    endDate: string,
    participations: IParticipation[]
}