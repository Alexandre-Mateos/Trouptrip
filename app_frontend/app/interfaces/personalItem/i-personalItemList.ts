import type {IPersonalItem} from "~/interfaces/personalItem/I-presonalItem";

export interface IPersonalItemList{
    member: IPersonalItem[];
    totalItems?: number;
    "@context"?: string;
    "@id"?: string;
    "@type"?: string;
}