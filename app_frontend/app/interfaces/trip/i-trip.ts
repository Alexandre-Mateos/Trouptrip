import type {IApiResource} from "~/interfaces/i-apiResource";

export interface ITrip extends IApiResource{
    id: number;
    title: string;
    startDate: string;
    endDate: string;
}