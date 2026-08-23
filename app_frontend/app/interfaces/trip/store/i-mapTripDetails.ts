import type {ITripOwner} from "~/interfaces/trip/i-tripOwner";
import type {IBaseMapTrip} from "~/interfaces/trip/store/i-baseMapTrip";

export interface IMapTripDetails extends IBaseMapTrip{
    isDetail: true;
    description: string;
    participationIds: number[];
    owner: ITripOwner;
    groupItemIds: number[],
    personalItemIds: number[]
}