import type {IApiResource} from "~/interfaces/i-apiResource";

export interface IBaseMapTrip extends IApiResource {
    id: number;
    title: string;
    startDate: string;
    endDate: string;
}