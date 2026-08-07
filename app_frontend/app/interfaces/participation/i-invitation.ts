import type {IApiResource} from "~/interfaces/i-apiResource";
import type {ITrip} from "~/interfaces/trip/i-trip";
import type {ITripOwner} from "~/interfaces/trip/i-tripOwner";

export interface IInvitation extends IApiResource{
    id: number,
    trip: ITrip,
    invitedBy:ITripOwner
}